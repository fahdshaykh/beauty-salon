<?php

namespace App\Services\Bookings;

use App\ServiceBooking;
use App\Customer;
use App\Mail\BookingMailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event as GoogleCalendarEvent;
use Spatie\GoogleCalendar\GoogleCalendar;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_EventAttendee;
use Google\Service\Exception as GoogleServiceException;
use App\Http\Controllers\GoogleCalendarOAuthController;
use Throwable;

class BookingNotificationService
{
    /**
     * Check SMS credits with the gateway.
     */
    public function checkCredits(int $companyId)
    {
        $settings = DB::table('company_settings')->where('company_id', $companyId)->first();

        if (!$settings || empty($settings->voodoosms_api_key)) {
            return (object)['error' => (object)['msg' => 'SMS Gateway API key not configured']];
        }

        $apiKey = $settings->voodoosms_api_key;
        $ch = curl_init('https://api.voodoosms.com/credits');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $apiKey]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Send booking SMS via Voodoo SMS gateway.
     */
    public function sendMessage(int $bookingId, string $action, int $companyId)
    {
        $settings = DB::table('company_settings')->where('company_id', $companyId)->first();
        if (!$settings) {
            return (object)['error' => (object)['msg' => 'Company settings not found']];
        }

        $response = $this->checkCredits($companyId);
        if (!isset($response->amount)) {
            return $response;
        }

        $booking = ServiceBooking::with(['customer', 'companysetting'])->where('id', $bookingId)->first();
        if (!$booking || !$booking->customer) {
            return (object)['error' => (object)['msg' => 'Booking or customer record not found']];
        }

        $customer     = trim($booking->customer->first_name . ' ' . $booking->customer->last_name);
        $dueDate      = $booking->due_date;
        $dueTime      = $booking->due_time;
        $mobileNumber = $booking->customer->primary_phone;
        $companyName  = $booking->companysetting->site_name ?? '';
        $smartName    = $booking->companysetting->smart_name ?? '';

        if ($action === 'Booking') {
            $bookingSms = $booking->companysetting->booking_sms ?? '';
        } elseif ($action === 'Survey') {
            $bookingSms = $booking->companysetting->survey_sms ?? '';
        } else {
            $bookingSms = $booking->companysetting->re_sheduled_sms ?? '';
        }

        $services = '';
        if ($action !== 'Survey') {
            $servicenames = DB::table('booking_details')
                ->join('services', 'services.id', '=', 'booking_details.service_id')
                ->where('booking_details.booking_id', $booking->id)
                ->pluck('services.name')
                ->toArray();
            $services = implode(', ', $servicenames);
        }

        $companyFrom = ($smartName === '') ? $companyName : $smartName;
        $apiKey      = $settings->voodoosms_api_key;

        $body = json_encode([
            'to'           => $mobileNumber,
            'from'         => $companyFrom,
            'country_code' => $booking->customer->country_code ?? '44',
            'dynamic_data' => [
                [
                    'customer name' => $customer,
                    'services name' => $services,
                    'date'          => date('d-m-Y', strtotime($dueDate)),
                    'time'          => $dueTime
                ]
            ],
            'msg'     => $bookingSms,
            'sandbox' => false
        ]);

        $curl = curl_init('https://api.voodoosms.com/sendsms');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $res = curl_exec($curl);
        curl_close($curl);

        return json_decode($res);
    }

    /**
     * Send booking confirmation email asynchronously.
     */
    public function sendConfirmationEmail(int $bookingId, int $companyId)
    {
        $setting = DB::table('company_settings')->where('company_id', $companyId)->first();
        if (!$setting || $setting->email_permissions != 1) {
            return;
        }

        try {
            $booking = ServiceBooking::findOrFail($bookingId);
            $customer = Customer::findOrFail($booking->customer_id);

            if ($customer->email) {
                $services = DB::table('booking_details')
                    ->join('services', 'services.id', '=', 'booking_details.service_id')
                    ->where('booking_details.booking_id', $booking->id)
                    ->pluck('services.name')
                    ->implode(', ');

                $totalPrice = $booking->total_price;
                $vatAmount  = ($totalPrice * ($setting->vat ?? 0)) / 100;

                $tz = $setting->timezone ?: config('app.timezone', 'UTC');
                $start = Carbon::parse($booking->due_date . ' ' . $booking->due_time, $tz);
                $duration = (!empty($booking->service_time) && (int)$booking->service_time > 0) ? (int)$booking->service_time : 60;
                $end = $start->copy()->addMinutes($duration);

                $googleCalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
                    . '&text=' . urlencode(($setting->site_name ?? 'Beauty Salon') . ' - ' . ($services ?: 'Appointment'))
                    . '&dates=' . $start->copy()->utc()->format('Ymd\THis\Z') . '/' . $end->copy()->utc()->format('Ymd\THis\Z')
                    . '&details=' . urlencode("Booking: BK-" . str_pad($booking->id, 5, '0', STR_PAD_LEFT) . "\nServices: {$services}\nTotal: £" . number_format($totalPrice, 2))
                    . '&location=' . urlencode($booking->is_home_service && !empty($booking->booking_address) ? $booking->booking_address : ($setting->site_name ?? ''));

                $bookingData = [
                    'customer_name'       => $customer->customer_name ?? trim($customer->first_name . ' ' . $customer->last_name),
                    'invoice_number'      => 'BK-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                    'due_date'            => date('d/m/Y', strtotime($booking->due_date)),
                    'due_time'            => $booking->due_time,
                    'services'            => $services,
                    'total_price'         => number_format($totalPrice, 2),
                    'vat'                 => number_format($vatAmount, 2),
                    'comment'             => $booking->comment ?? '',
                    'company_title'       => $setting->site_name ?? 'Beauty Salon',
                    'google_calendar_url' => $googleCalUrl,
                ];

                Mail::to($customer->email)->queue(new BookingMailable(
                    $bookingData,
                    $customer->email,
                    'Booking Confirmation',
                    $setting->from_email ?? 'noreply@salon.com',
                    $setting->site_name ?? 'Beauty Salon',
                    $companyId
                ));
            }
        } catch (\Exception $e) {
            Log::error('Booking email error: ' . $e->getMessage());
        }
    }

    /**
     * Send booking completion email asynchronously.
     */
    public function sendCompletionEmail(int $bookingId, int $companyId)
    {
        $setting = DB::table('company_settings')->where('company_id', $companyId)->first();
        if (!$setting || empty($setting->complete_booking_email_permission)) {
            return;
        }

        try {
            $booking = ServiceBooking::findOrFail($bookingId);
            $customer = Customer::findOrFail($booking->customer_id);

            if ($customer->email) {
                $services = DB::table('booking_details')
                    ->join('services', 'services.id', '=', 'booking_details.service_id')
                    ->where('booking_details.booking_id', $booking->id)
                    ->pluck('services.name')
                    ->implode(', ');

                $totalPrice = $booking->total_price;
                $vatAmount  = ($totalPrice * ($setting->vat ?? 0)) / 100;

                $bookingData = [
                    'customer_name'       => $customer->customer_name ?? trim($customer->first_name . ' ' . $customer->last_name),
                    'invoice_number'      => 'BK-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                    'due_date'            => date('d/m/Y', strtotime($booking->due_date)),
                    'due_time'            => $booking->due_time,
                    'services'            => $services,
                    'total_price'         => number_format($totalPrice, 2),
                    'vat'                 => number_format($vatAmount, 2),
                    'amount_paid'         => number_format($booking->amount_paid ?? 0, 2),
                    'amount_pending'      => number_format($booking->amount_pending ?? 0, 2),
                    'comment'             => $booking->comment ?? '',
                    'company_title'       => $setting->site_name ?? 'Beauty Salon',
                    'is_completed'        => true,
                ];

                Mail::to($customer->email)->queue(new BookingMailable(
                    $bookingData,
                    $customer->email,
                    'Booking Completed - ' . ($setting->site_name ?? 'Beauty Salon'),
                    $setting->from_email ?? 'noreply@salon.com',
                    $setting->site_name ?? 'Beauty Salon',
                    $companyId
                ));
            }
        } catch (\Exception $e) {
            Log::error('Booking completion email error: ' . $e->getMessage());
        }
    }

    /**
     * Get an authenticated Google_Client for a company.
     * Prioritizes OAuth (connected salon owner account) and falls back to central Service Account.
     * Automatically refreshes expired OAuth access tokens using refresh tokens.
     */
    public function getGoogleClientForCompany(int $companyId, &$setting = null): ?Google_Client
    {
        if (!$setting) {
            $setting = DB::table('company_settings')->where('company_id', $companyId)->first();
        }

        if (!$setting || empty($setting->google_calendar_sync)) {
            return null;
        }

        // 1. Try OAuth (Salon Owner's connected Google account)
        if (!empty($setting->google_access_token)) {
            $tokenData = json_decode($setting->google_access_token, true);
            if (is_array($tokenData)) {
                $client = GoogleCalendarOAuthController::createOAuthClient($companyId);
                if ($client) {
                    $client->setAccessToken($tokenData);

                    // Check and refresh expired token
                    if ($client->isAccessTokenExpired()) {
                        $refreshToken = $client->getRefreshToken() ?: ($tokenData['refresh_token'] ?? null);
                        if ($refreshToken) {
                            $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                            if (!isset($newToken['error'])) {
                                if (!isset($newToken['refresh_token'])) {
                                    $newToken['refresh_token'] = $refreshToken;
                                }
                                DB::table('company_settings')->where('company_id', $companyId)->update([
                                    'google_access_token' => json_encode($newToken),
                                    'updated_at'          => now(),
                                ]);
                                $client->setAccessToken($newToken);
                                $setting->google_access_token = json_encode($newToken);
                            } else {
                                Log::warning("Failed to refresh Google OAuth token for company {$companyId}: " . json_encode($newToken));
                            }
                        }
                    }

                    return $client;
                }
            }
        }

        // 2. Fallback to central Service Account if configured
        $credentialsPath = config('google-calendar.auth_profiles.service_account.credentials_json');
        if (!empty($credentialsPath) && file_exists($credentialsPath)) {
            $client = new Google_Client();
            $client->setScopes([Google_Service_Calendar::CALENDAR]);
            $client->setAuthConfig($credentialsPath);
            return $client;
        }

        return null;
    }

    /**
     * Synchronize a booking event to the salon's Google Calendar.
     * Invites the customer as a guest and sends Google Calendar invitations (sendUpdates = 'all').
     *
     * @param int $bookingId
     * @param int $companyId
     * @return string|null The Google Event ID, or null if skipped/failed
     */
    public function syncGoogleCalendarEvent(int $bookingId, int $companyId): ?string
    {
        $setting = DB::table('company_settings')->where('company_id', $companyId)->first();
        $client = $this->getGoogleClientForCompany($companyId, $setting);
        if (!$client) {
            return null;
        }

        $calendarId = trim($setting->google_calendar_id ?? '') ?: 'primary';
        $isOAuth = !empty($setting->google_access_token);

        // Also keep Spatie config in sync
        config(['google-calendar.calendar_id' => $calendarId]);

        try {
            $booking = ServiceBooking::with(['customer', 'user'])->find($bookingId);
            if (!$booking) {
                return null;
            }

            $customer = $booking->customer;
            $customerName = $customer ? ($customer->customer_name ?? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''))) : 'Customer';
            $customerEmail = $customer ? trim($customer->email ?? '') : '';

            $serviceNames = DB::table('booking_details')
                ->join('services', 'services.id', '=', 'booking_details.service_id')
                ->where('booking_details.booking_id', $booking->id)
                ->pluck('services.name')
                ->implode(', ');

            if (empty($serviceNames)) {
                $serviceNames = 'Salon Appointment';
            }

            $salonName = $setting->site_name ?? 'Beauty Salon';
            $tz = $setting->timezone ?: config('app.timezone', 'UTC');

            $dateStr = $booking->due_date ? Carbon::parse($booking->due_date)->format('Y-m-d') : Carbon::today($tz)->format('Y-m-d');
            $timeStr = $booking->due_time ?: '09:00:00';
            $start = Carbon::parse($dateStr . ' ' . $timeStr, $tz);
            $durationMinutes = (!empty($booking->service_time) && (int)$booking->service_time > 0) ? (int)$booking->service_time : 60;
            $end = $start->copy()->addMinutes($durationMinutes);

            $summary = $salonName . ' - ' . $serviceNames . ($customerName ? ' (' . $customerName . ')' : '');

            $descriptionLines = [
                "Salon: {$salonName}",
                "Booking Reference: BK-" . str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                "Customer: {$customerName}",
                "Services: {$serviceNames}",
            ];
            if (!empty($customer->primary_phone)) {
                $descriptionLines[] = "Phone: {$customer->primary_phone}";
            }
            if (!empty($customerEmail)) {
                $descriptionLines[] = "Email: {$customerEmail}";
            }
            if (!empty($booking->total_price)) {
                $symbol = $setting->currency_symbol ?? '£';
                $descriptionLines[] = "Total Price: {$symbol}" . number_format($booking->total_price, 2);
            }
            if (!empty($booking->comment)) {
                $descriptionLines[] = "Notes: {$booking->comment}";
            }

            if (!empty($booking->is_home_service) && !empty($booking->booking_address)) {
                $location = $booking->booking_address;
                $descriptionLines[] = "Service Location: Home Service ({$booking->booking_address})";
            } else {
                $location = $salonName;
            }

            $calendarService = new Google_Service_Calendar($client);

            // Check if existing event exists on Google Calendar
            $googleEvent = null;
            if (!empty($booking->google_event_id)) {
                try {
                    $googleEvent = $calendarService->events->get($calendarId, $booking->google_event_id);
                } catch (GoogleServiceException $ge) {
                    if ($ge->getCode() == 404) {
                        Log::info("Google event {$booking->google_event_id} returned 404 on calendar {$calendarId}. Recreating event.");
                        $googleEvent = null;
                        $booking->update(['google_event_id' => null]);
                    } else {
                        throw $ge;
                    }
                }
            }

            $isNew = false;
            if (!$googleEvent) {
                $googleEvent = new Google_Service_Calendar_Event();
                $isNew = true;
            }

            $googleEvent->setSummary($summary);
            $googleEvent->setDescription(implode("\n", $descriptionLines));
            $googleEvent->setLocation($location);

            $startDateTime = new Google_Service_Calendar_EventDateTime();
            $startDateTime->setDateTime($start->toRfc3339String());
            $startDateTime->setTimeZone($tz);
            $googleEvent->setStart($startDateTime);

            $endDateTime = new Google_Service_Calendar_EventDateTime();
            $endDateTime->setDateTime($end->toRfc3339String());
            $endDateTime->setTimeZone($tz);
            $googleEvent->setEnd($endDateTime);

            // Add customer as an attendee so Google dispatches calendar invitations
            // Note: OAuth allows adding attendees. Standard service accounts require Domain-Wide Delegation.
            $canAddAttendees = $isOAuth || !empty(config('google-calendar.user_to_impersonate'));
            if (!empty($customerEmail) && $canAddAttendees) {
                $attendee = new Google_Service_Calendar_EventAttendee([
                    'email'          => $customerEmail,
                    'displayName'    => $customerName,
                    'responseStatus' => 'needsAction',
                ]);
                $googleEvent->setAttendees([$attendee]);
            }

            $optParams = ['sendUpdates' => 'all'];

            try {
                if ($isNew) {
                    $resultEvent = $calendarService->events->insert($calendarId, $googleEvent, $optParams);
                } else {
                    $resultEvent = $calendarService->events->update($calendarId, $googleEvent->getId(), $googleEvent, $optParams);
                }
            } catch (GoogleServiceException $ge) {
                // If service account without delegation rejected attendees, fallback without attendees
                if (str_contains($ge->getMessage(), 'forbiddenForServiceAccounts')) {
                    $googleEvent->setAttendees([]);
                    if ($isNew) {
                        $resultEvent = $calendarService->events->insert($calendarId, $googleEvent, []);
                    } else {
                        $resultEvent = $calendarService->events->update($calendarId, $googleEvent->getId(), $googleEvent, []);
                    }
                } else {
                    throw $ge;
                }
            }

            if ($resultEvent && $resultEvent->getId()) {
                $booking->update(['google_event_id' => $resultEvent->getId()]);
                return $resultEvent->getId();
            }

            return null;
        } catch (Throwable $e) {
            Log::error("Google Calendar sync error for booking {$bookingId}: " . $e->getMessage(), [
                'exception'  => $e,
                'company_id' => $companyId,
            ]);
            return null;
        }
    }

    /**
     * Delete or cancel a Google Calendar event for a cancelled booking.
     * Notifies attendees with sendUpdates = 'all'.
     */
    public function deleteGoogleCalendarEvent(int $bookingId, int $companyId): bool
    {
        $setting = DB::table('company_settings')->where('company_id', $companyId)->first();
        $client = $this->getGoogleClientForCompany($companyId, $setting);
        if (!$client) {
            return false;
        }

        try {
            $booking = ServiceBooking::find($bookingId);
            if (!$booking || empty($booking->google_event_id)) {
                return false;
            }

            $calendarId = trim($setting->google_calendar_id ?? '') ?: 'primary';
            $calendarService = new Google_Service_Calendar($client);

            try {
                $calendarService->events->delete($calendarId, $booking->google_event_id, [
                    'sendUpdates' => 'all',
                ]);
            } catch (GoogleServiceException $ge) {
                if ($ge->getCode() == 404) {
                    Log::info("Google event {$booking->google_event_id} was already deleted (404).");
                } else {
                    throw $ge;
                }
            }

            $booking->update(['google_event_id' => null]);
            return true;
        } catch (Throwable $e) {
            Log::error("Google Calendar event deletion error for booking {$bookingId}: " . $e->getMessage(), [
                'exception'  => $e,
                'company_id' => $companyId,
            ]);
            return false;
        }
    }
}


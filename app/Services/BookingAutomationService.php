<?php

namespace App\Services;

use App\Models\BookingAutomation;
use App\ServiceBooking;
use App\Company;
use App\CompanySetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingAutomationService
{
    /**
     * Trigger an automation event for a booking.
     */
    public function trigger(string $event, ServiceBooking $booking): bool
    {
        $automation = BookingAutomation::where('company_id', $booking->company_id)
            ->where('trigger_event', $event)
            ->where('is_enabled', true)
            ->first();

        if (!$automation) {
            return false;
        }

        $booking->loadMissing(['customer', 'user', 'services']);
        $company = Company::find($booking->company_id);

        $channel = $automation->channel; // 'sms', 'email', 'both'

        if (in_array($channel, ['sms', 'both']) && !empty($automation->sms_template)) {
            $smsContent = $this->parseTemplate($automation->sms_template, $booking, $company);
            $phone = optional($booking->customer)->primary_phone;
            if (!empty($phone)) {
                $this->sendSms($booking->company_id, $phone, $smsContent);
            }
        }

        if (in_array($channel, ['email', 'both']) && !empty($automation->email_template)) {
            $emailSubject = $this->parseTemplate($automation->email_subject ?? 'Service Booking Update', $booking, $company);
            $emailContent = $this->parseTemplate($automation->email_template, $booking, $company);
            $email = optional($booking->customer)->email;
            if (!empty($email)) {
                $this->sendEmail($email, $emailSubject, $emailContent, $company);
            }
        }

        return true;
    }

    /**
     * Replace template merge tags with actual booking information.
     */
    public function parseTemplate(string $template, ServiceBooking $booking, ?Company $company = null): string
    {
        $customerName = optional($booking->customer)->first_name . ' ' . optional($booking->customer)->last_name;
        $customerName = trim($customerName) ?: 'Valued Customer';

        $companyName  = optional($company)->company_name ?? 'Our Company';
        $serviceNames = $booking->services->pluck('name')->implode(', ');
        if (empty($serviceNames)) {
            $serviceNames = 'Booked Service';
        }

        $technicianName = optional($booking->user)->name ?? 'Assigned Specialist';
        $dueTime = $booking->due_time ? Carbon::parse($booking->due_time)->format('h:i A') : 'Scheduled Time';
        $dueDate = $booking->due_date ? Carbon::parse($booking->due_date)->format('d M, Y') : 'Scheduled Date';
        $address = $booking->is_home_service
            ? ($booking->booking_address ?? 'Your Service Address')
            : (optional($company)->company_address ?? 'Our Facility');

        $trackingLink = url('/book/' . optional($company->bookingPageSetting)->slug . '/confirmed/' . $booking->invoice);
        $reviewLink   = url('/book/' . optional($company->bookingPageSetting)->slug);
        $bookingLink  = url('/book/' . optional($company->bookingPageSetting)->slug);

        $placeholders = [
            '{customer_name}'   => $customerName,
            '{service_name}'    => $serviceNames,
            '{due_date}'        => $dueDate,
            '{due_time}'        => $dueTime,
            '{technician_name}' => $technicianName,
            '{company_name}'    => $companyName,
            '{booking_address}' => $address,
            '{address}'         => $address,
            '{invoice_no}'      => $booking->invoice ?? ('#' . $booking->id),
            '{total_price}'     => number_format((float)$booking->total_price, 2),
            '{tracking_link}'   => $trackingLink,
            '{review_link}'     => $reviewLink,
            '{booking_link}'    => $bookingLink,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Send SMS using company Voodoo SMS integration or log safely.
     */
    protected function sendSms(int $companyId, string $phone, string $message): bool
    {
        try {
            $settings = DB::table('company_settings')->where('company_id', $companyId)->first();
            $apiKey = $settings->voodoosms_api_key ?? null;

            if (!empty($apiKey) && ($settings->sms_count ?? 0) > 0) {
                $ch = curl_init('https://api.voodoosms.com/sendsms');
                curl_setopt_array($ch, [
                    CURLOPT_POST           => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS     => [
                        'dest'    => $phone,
                        'orig'    => substr(optional(Company::find($companyId))->company_name ?: 'Booking', 0, 11),
                        'msg'     => $message,
                        'apikey'  => $apiKey,
                    ],
                    CURLOPT_TIMEOUT        => 5,
                ]);
                $response = curl_exec($ch);
                curl_close($ch);

                DB::table('company_settings')->where('company_id', $companyId)->decrement('sms_count');
                Log::info("Automation SMS dispatched to {$phone}: {$message}");
                return true;
            } else {
                Log::info("[DEV/MOCK] Automation SMS to {$phone}: {$message}");
                return true;
            }
        } catch (\Exception $e) {
            Log::warning("Automation SMS failed for {$phone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email via Laravel Mail or log safely.
     */
    protected function sendEmail(string $recipient, string $subject, string $body, ?Company $company = null): bool
    {
        try {
            Mail::raw($body, function ($msg) use ($recipient, $subject, $company) {
                $fromName = optional($company)->company_name ?? config('mail.from.name', 'Service Booking');
                $msg->to($recipient)
                    ->subject($subject);
                if (config('mail.from.address')) {
                    $msg->from(config('mail.from.address'), $fromName);
                }
            });
            Log::info("Automation Email dispatched to {$recipient}: {$subject}");
            return true;
        } catch (\Exception $e) {
            Log::warning("Automation Email failed for {$recipient}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process 24-hour and 2-hour reminders (called by scheduler).
     */
    public function processReminders(): int
    {
        $sentCount = 0;
        $now = now();

        // 1. Process 24-Hour Reminders (appointments between 23h and 25h from now)
        $start24h = $now->copy()->addHours(23);
        $end24h   = $now->copy()->addHours(25);

        $bookings24h = ServiceBooking::whereBetween('due_date', [$start24h->toDateString(), $end24h->toDateString()])
            ->whereNotIn('service_status', [3, 4]) // Not completed, not cancelled
            ->get();

        foreach ($bookings24h as $b) {
            $scheduledTime = Carbon::parse($b->due_date . ' ' . $b->due_time);
            if ($scheduledTime->between($start24h, $end24h)) {
                $this->trigger('reminder_24h', $b);
                $sentCount++;
            }
        }

        // 2. Process 2-Hour Reminders (appointments between 1h 30m and 2h 30m from now)
        $start2h = $now->copy()->addMinutes(90);
        $end2h   = $now->copy()->addMinutes(150);

        $bookings2h = ServiceBooking::where('due_date', $now->toDateString())
            ->whereNotIn('service_status', [3, 4])
            ->get();

        foreach ($bookings2h as $b) {
            $scheduledTime = Carbon::parse($b->due_date . ' ' . $b->due_time);
            if ($scheduledTime->between($start2h, $end2h)) {
                $this->trigger('reminder_2h', $b);
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Process 30-day customer recalls (called by scheduler).
     */
    public function processRecalls(): int
    {
        $targetDate = now()->subDays(30)->toDateString();

        $completedBookings = ServiceBooking::where('due_date', $targetDate)
            ->where('service_status', 3) // Completed
            ->get();

        $count = 0;
        foreach ($completedBookings as $b) {
            $this->trigger('recall_30d', $b);
            $count++;
        }

        return $count;
    }
}

<?php

namespace App\Services\Bookings;

use App\ServiceBooking;
use App\Customer;
use App\Mail\BookingMailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

                $bookingData = [
                    'customer_name'  => $customer->customer_name ?? trim($customer->first_name . ' ' . $customer->last_name),
                    'invoice_number' => 'BK-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                    'due_date'       => date('d/m/Y', strtotime($booking->due_date)),
                    'due_time'       => $booking->due_time,
                    'services'       => $services,
                    'total_price'    => number_format($totalPrice, 2),
                    'vat'            => number_format($vatAmount, 2),
                    'comment'        => $booking->comment ?? '',
                    'company_title'  => $setting->site_name ?? 'Beauty Salon',
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
}


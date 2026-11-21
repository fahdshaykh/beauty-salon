<?php

namespace App\Services\Bookings;

use App\ServiceBooking;
use App\BookingDetail;
use App\InventoryRemain;
use App\Helpers\LimitHelper;
use App\Models\BookingMessage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingActionService
{
    public function __construct(
        protected BookingNotificationService $notificationService,
        protected BookingAvailabilityService $availabilityService
    ) {}

    /**
     * Check if a given date string corresponds to a new financial year.
     */
    public function checkIfFinancialYear(?string $financialYear): bool
    {
        if (empty($financialYear)) {
            return false;
        }

        $today = date('m-d');
        return $today === date('m-d', strtotime($financialYear));
    }

    /**
     * Create a new booking transactionally with thread-safe invoice numbering.
     */
    public function createBooking(int $companyId, array $data, $authUser): array
    {
        $userId  = $data['user_id'];
        $dueDate = $data['due_date'];
        $dueTime = Carbon::parse($data['due_time'])->format('H:i:s');
        $serviceTime = (int)($data['service_time'] ?? 30);

        // Check availability
        $availability = $this->availabilityService->checkAvailability(
            $companyId,
            $userId,
            $dueDate,
            $dueTime,
            $serviceTime
        );

        if (!$availability['available']) {
            return [
                'success' => false,
                'message' => 'A booking already exists within the selected time window. Please choose another slot.'
            ];
        }

        $booking = DB::transaction(function () use ($companyId, $data, $userId, $dueDate, $dueTime, $serviceTime) {
            $taxInfo = DB::table('company_settings')
                ->select('tax_status', 'vat', 'amount', 'financial_year')
                ->where('company_id', $companyId)
                ->first();

            $isNewFinancialYear = false;
            if ($taxInfo && !empty($taxInfo->financial_year)) {
                $isNewFinancialYear = $this->checkIfFinancialYear($taxInfo->financial_year);
            }

            // Thread-safe invoice generation using pessimistic lock
            if ($isNewFinancialYear) {
                $nextInvoice = 1;
            } else {
                $lastInvoice = ServiceBooking::where('company_id', $companyId)
                    ->whereNotNull('invoice')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->value('invoice');
                $nextInvoice = ($lastInvoice ? (int)$lastInvoice : 0) + 1;
            }

            $isDiscount = !empty($data['is_discount']) ? 1 : 0;
            $discountAmount = 0.0;

            if ($isDiscount) {
                $submittedTotal = (float)($data['total_price'] ?? 0);
                $advance = (float)($data['advance_amount'] ?? 0);
                $discountAmount = isset($data['discount_amount']) && (float)$data['discount_amount'] > 0
                    ? (float)$data['discount_amount']
                    : max(0.0, $submittedTotal - $advance);

                if ($advance > 0) {
                    $data['total_price'] = $advance;
                }
            }

            $vat = 0;
            if ($taxInfo && $taxInfo->tax_status == 1 && !empty($taxInfo->amount) && ($data['survey'] ?? '') === 'no') {
                $vat = $taxInfo->vat == 1
                    ? ($data['total_price'] * $taxInfo->amount / 100)
                    : (float)$taxInfo->amount;
            }

            $totalAmount = (float)($data['total_price'] ?? 0) + (float)$vat;
            $advanceAmount = 0.0;
            $hasAdvance = !empty($data['collect_advance']) && isset($data['advance_amount']) && (float)$data['advance_amount'] > 0;
            if ($hasAdvance) {
                $advanceAmount = min($totalAmount, (float)$data['advance_amount']);
            }

            $amountPaid = $advanceAmount;
            $amountPending = max(0.0, $totalAmount - $amountPaid);
            $paymentStatus = ($amountPending <= 0.0 && $totalAmount > 0) ? 'paid' : ($amountPaid > 0.0 ? 'partial' : 'unpaid');

            $paymentMethod = $hasAdvance ? ($data['payment_method'] ?? 'cash') : null;
            $paymentDetails = $hasAdvance && !empty($data['payment_details']) 
                ? (is_array($data['payment_details']) ? json_encode($data['payment_details']) : $data['payment_details']) 
                : null;

            $paymentInstrumentNumber = null;
            if ($hasAdvance) {
                if ($paymentMethod === 'cheque' && !empty($data['cheque_number'])) {
                    $paymentInstrumentNumber = $data['cheque_number'];
                } elseif (!empty($data['payment_details']) && is_array($data['payment_details'])) {
                    $firstVal = reset($data['payment_details']);
                    if (!empty($firstVal) && is_string($firstVal)) {
                        $paymentInstrumentNumber = $firstVal;
                    }
                }
            }

            $booking = ServiceBooking::create([
                'invoice'                   => $nextInvoice,
                'user_id'                   => $userId,
                'company_id'                => $companyId,
                'customer_id'               => $data['customer_id'],
                'total_price'               => $data['total_price'],
                'due_date'                  => $dueDate,
                'due_time'                  => $dueTime,
                'service_time'              => $serviceTime,
                'comment'                   => $data['comment'] ?? null,
                'vat'                       => $vat,
                'amount_paid'               => $amountPaid,
                'amount_pending'            => $amountPending,
                'payment_status'            => $paymentStatus,
                'is_discount'               => $isDiscount,
                'discount_amount'           => $discountAmount,
                'payment_instrument'        => $paymentMethod,
                'payment_instrument_number' => $paymentInstrumentNumber,
                'payment_details'           => $paymentDetails,
                'service_status'            => '1', // Booked
                'payment_days'              => 0,
                'is_home_service'           => !empty($data['is_home_service']) ? 1 : 0,
                'booking_address'           => !empty($data['is_home_service']) ? ($data['booking_address'] ?? null) : null,
            ]);

            if ($hasAdvance && $advanceAmount > 0) {
                DB::table('booking_payments')->insert([
                    'company_id'      => $companyId,
                    'booking_id'      => $booking->id,
                    'amount'          => $advanceAmount,
                    'payment_method'  => $paymentMethod ?: 'cash',
                    'payment_details' => $paymentDetails,
                    'payment_file'    => $data['payment_file_name'] ?? null,
                    'payment_date'    => !empty($data['advance_payment_date']) ? date('Y-m-d', strtotime($data['advance_payment_date'])) : date('Y-m-d'),
                    'payment_type'    => ($amountPending <= 0) ? 'final' : 'advance',
                    'notes'           => $data['payment_notes'] ?? 'Advance payment received at booking creation',
                    'created_by'      => $authUser->id ?? null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            // Insert service details
            if (!empty($data['service_id']) && is_array($data['service_id'])) {
                foreach ($data['service_id'] as $serviceId) {
                    DB::table('booking_details')->insert([
                        'booking_id' => $booking->id,
                        'service_id' => $serviceId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Deduct inventory items
            if (!empty($data['inventory_id']) && is_array($data['inventory_id'])) {
                foreach ($data['inventory_id'] as $index => $inventoryId) {
                    if (empty($inventoryId)) continue;
                    $quantity = isset($data['quantity'][$index]) && is_numeric($data['quantity'][$index])
                        ? (float)$data['quantity'][$index]
                        : 0;

                    if ($quantity <= 0) continue;

                    DB::table('inventory_books')->insert([
                        'booking_id'   => $booking->id,
                        'company_id'   => $companyId,
                        'inventory_id' => $inventoryId,
                        'quantity'     => $quantity,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    $remain = InventoryRemain::firstOrNew([
                        'inventory_id' => $inventoryId,
                        'company_id'   => $companyId
                    ]);

                    if (!$remain->exists) {
                        $remain->total_quantity = 0;
                        $remain->company_id     = $companyId;
                    }

                    $remain->total_quantity = max(0, $remain->total_quantity - $quantity);
                    $remain->save();
                }
            }

            LimitHelper::increment('bookings');

            return $booking;
        });

        // Notifications
        $this->notificationService->sendConfirmationEmail($booking->id, $companyId);
        $this->notificationService->syncGoogleCalendarEvent($booking->id, $companyId);

        $smsStatus = DB::table('company_settings')->where('company_id', $companyId)->first();
        if ($smsStatus && $smsStatus->message_permissions == 1) {
            if ($smsStatus->sms_count <= 0) {
                return [
                    'success' => true,
                    'message' => 'Booking created successfully. Message not sent due to insufficient SMS balance.',
                    'booking' => $booking
                ];
            }

            $response = $this->notificationService->sendMessage($booking->id, 'Booking', $companyId);
            if (isset($response->error)) {
                return [
                    'success' => true,
                    'message' => 'Booking created successfully. Message not sent: ' . ($response->error->msg ?? 'Unknown error'),
                    'booking' => $booking
                ];
            }

            DB::table('company_settings')->where('company_id', $companyId)->decrement('sms_count');
            return [
                'success' => true,
                'message' => 'Booking created successfully & Message sent to customer.',
                'booking' => $booking
            ];
        }

        return [
            'success' => true,
            'message' => 'Booking created successfully.',
            'booking' => $booking
        ];
    }

    /**
     * Cancel a booking transactionally.
     */
    public function cancelBooking(int $bookingId, int $companyId, ?string $reason = null): bool
    {
        $cancelled = DB::transaction(function () use ($bookingId, $companyId, $reason) {
            $booking = ServiceBooking::where('company_id', $companyId)
                ->where('id', $bookingId)
                ->firstOrFail();

            $booking->service_status = 4; // Cancelled
            $booking->save();

            BookingMessage::create([
                'booking_id' => $bookingId,
                'reason'     => $reason,
            ]);

            return true;
        });

        // Delete from Google Calendar & notify attendees
        $this->notificationService->deleteGoogleCalendarEvent($bookingId, $companyId);

        return $cancelled;
    }
}


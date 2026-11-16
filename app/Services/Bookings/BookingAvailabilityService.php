<?php

namespace App\Services\Bookings;

use App\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingAvailabilityService
{
    /**
     * Check if a staff member is available at the requested date and time.
     * Performs a mathematically sound two-way interval overlap check:
     * (new_start < existing_end) AND (new_end > existing_start)
     */
    public function checkAvailability(
        int $companyId,
        int $userId,
        string $dueDate,
        string $rawDueTime,
        int $serviceTime = 30,
        ?int $ignoreBookingId = null
    ): array {
        $dueTime = Carbon::parse($rawDueTime)->format('H:i:s');

        $query = ServiceBooking::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('due_date', $dueDate)
            ->whereNotIn('service_status', [4]); // Ignore cancelled bookings

        if ($ignoreBookingId) {
            $query->where('id', '!=', $ignoreBookingId);
        }

        // Two-way overlap:
        // Slot A: [due_time, due_time + service_time]
        // Slot B: [existing.due_time, existing.due_time + existing.service_time]
        $query->where(function ($q) use ($dueTime, $serviceTime) {
            $q->whereRaw("? < ADDTIME(due_time, SEC_TO_TIME(service_time * 60))", [$dueTime])
              ->whereRaw("ADDTIME(?, SEC_TO_TIME(? * 60)) > due_time", [$dueTime, $serviceTime]);
        });

        $conflicts = $query->get(['id', 'due_date', 'due_time', 'service_time']);

        return [
            'available' => $conflicts->isEmpty(),
            'conflicts' => $conflicts,
        ];
    }
}


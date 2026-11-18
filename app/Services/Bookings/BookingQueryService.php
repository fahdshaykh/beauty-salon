<?php

namespace App\Services\Bookings;

use App\ServiceBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingQueryService
{
    /**
     * Build the filtered bookings query.
     */
    public function buildQuery(int $companyId, Request $request, ?int $scopedUserId = null)
    {
        $query = ServiceBooking::with(['customer', 'user', 'services', 'bookingdetail.service'])
            ->where('company_id', $companyId);

        if ($scopedUserId !== null) {
            $query->where('user_id', $scopedUserId);
        }

        // Apply service filter
        if ($request->filled('service_id')) {
            $query->whereHas('bookingdetail', function ($q) use ($request) {
                $q->where('service_id', $request->input('service_id'));
            });
        }

        // Apply staff user filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('due_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->input('end_date'));
        }

        // Apply customer name filter
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->input('customer_name') . '%']);
            });
        }

        // Apply primary phone filter
        if ($request->filled('primary_phone')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('primary_phone', 'LIKE', '%' . $request->input('primary_phone') . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('service_status', $request->input('status'));
        }

        // Apply invoice filter
        if ($request->filled('invoice')) {
            $query->where('invoice', $request->input('invoice'));
        }

        // Apply customer_id filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Format bookings into presentation data structure with overdue detection.
     * Preserves exact HTML keys and overdue warning flags.
     */
    public function formatBookingsForIndex($bookings): array
    {
        $dataArray = [];

        foreach ($bookings as $key => $value) {
            // 1. Fetch services with color via relationship or relational booking_details
            $servicesWithColor = $value->services->map(function ($service) {
                return [
                    'name'  => $service->name,
                    'color' => $service->service_color ?? '#6c757d',
                ];
            })->toArray();

            // Fallback to bookingdetail.service if hasManyThrough returned empty
            if (empty($servicesWithColor) && $value->relationLoaded('bookingdetail')) {
                $servicesWithColor = $value->bookingdetail->map(function ($detail) {
                    if ($detail->service) {
                        return [
                            'name'  => $detail->service->name,
                            'color' => $detail->service->service_color ?? '#6c757d',
                        ];
                    }
                    return null;
                })->filter()->values()->toArray();
            }

            $isOverdue = false;
            $overdueText = '';
            $overdueMinutes = 0;

            if ((int)$value->service_status === 2) {
                try {
                    // Use booking's total service_time or sum service_times from linked services
                    $serviceTime = (int)$value->service_time;
                    if ($serviceTime <= 0) {
                        $sumTime = $value->services->sum('service_time');
                        if ($sumTime <= 0 && $value->relationLoaded('bookingdetail')) {
                            $sumTime = $value->bookingdetail->sum(fn($d) => (int)($d->service?->service_time ?? 0));
                        }
                        $serviceTime = $sumTime > 0 ? $sumTime : 30;
                    }

                    $startDateTime = null;
                    if (!empty($value->due_date) && !empty($value->due_time)) {
                        $startDateTime = Carbon::parse($value->due_date . ' ' . $value->due_time);
                    } elseif (!empty($value->due_date)) {
                        $startDateTime = Carbon::parse($value->due_date);
                    }

                    $baseStart = $startDateTime ?? ($value->updated_at ? Carbon::parse($value->updated_at) : now());
                    $expectedEnd = (clone $baseStart)->addMinutes($serviceTime);

                    if ($value->updated_at) {
                        $updatedAt = Carbon::parse($value->updated_at);
                        if ($updatedAt->gt($baseStart)) {
                            $altEnd = (clone $updatedAt)->addMinutes($serviceTime);
                            if ($altEnd->gt($expectedEnd)) {
                                $expectedEnd = $altEnd;
                            }
                        }
                    }

                    if (now()->gt($expectedEnd)) {
                        $isOverdue = true;
                        $overdueMinutes = (int) abs(now()->diffInMinutes($expectedEnd));
                        if ($overdueMinutes >= 1440) {
                            $days = floor($overdueMinutes / 1440);
                            $overdueText = $days . 'd+';
                        } elseif ($overdueMinutes >= 60) {
                            $hours = floor($overdueMinutes / 60);
                            $mins = $overdueMinutes % 60;
                            $overdueText = $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                        } else {
                            $overdueText = ($overdueMinutes > 0 ? $overdueMinutes : 1) . 'm';
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore parse errors
                }
            }

            $dataArray[$key] = [
                'id'               => $value->id,
                'status'           => $value->status,
                'invoice'          => $value->invoice,
                'customer_name'    => optional($value->customer)->first_name . ' ' . optional($value->customer)->last_name,
                'primary_phone'    => optional($value->customer)->primary_phone,
                'customer_address' => optional($value->customer)->address,
                'customer_zip'     => optional($value->customer)->zip,
                'user_name'        => optional($value->user)->name,
                'price_type'       => $value->price_type,
                'total_price'      => $value->total_price,
                'due_date'         => $value->due_date,
                'due_time'         => $value->due_time,
                'service_time'     => $value->service_time,
                'service_status'   => $value->service_status,
                'services'         => $servicesWithColor,
                'payment_days'     => $value->payment_days,
                'is_overdue'       => $isOverdue,
                'overdue_text'     => $overdueText,
                'overdue_minutes'  => $overdueMinutes,
                'updated_at'       => $value->updated_at,
            ];
        }

        return $dataArray;
    }
}


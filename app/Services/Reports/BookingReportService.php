<?php

namespace App\Services\Reports;

use App\ServiceBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingReportService
{
    /**
     * Normalize an input date into standard Y-m-d format.
     */
    public function normalizeDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        $d = \DateTime::createFromFormat('d-m-Y', $date);
        if ($d) {
            return $d->format('Y-m-d');
        }

        return $date;
    }

    /**
     * Build the filtered query for booking reports.
     */
    public function buildQuery(int $companyId, array $filters, string $dateColumn = 'due_date')
    {
        $startDate = $this->normalizeDate($filters['start_date'] ?? null);
        $endDate   = $this->normalizeDate($filters['end_date'] ?? null);

        $query = ServiceBooking::where('company_id', $companyId)
            ->with([
                'user:id,name',
                'customer:id,first_name,last_name,address',
                'bookingdetail.service:id,name,category_id',
                'services'
            ]);

        // Date range filtering
        if ($startDate && $endDate) {
            if ($dateColumn === 'created_at') {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } else {
                $query->whereDate('due_date', '>=', $startDate)
                      ->whereDate('due_date', '<=', $endDate);
            }
        } elseif ($startDate) {
            $query->whereDate($dateColumn, '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate($dateColumn, '<=', $endDate);
        }

        // Status filter (ignores 'no', 'no_status', empty)
        $statusId = $filters['status_id'] ?? null;
        if (!empty($statusId) && !in_array($statusId, ['no', 'no_status', '0'], true)) {
            $query->where('service_status', $statusId);
        }

        // Payment instrument filter (ignores 'no', 'no_payment', empty)
        $paymentType = $filters['payment_instrument'] ?? $filters['payment_type'] ?? null;
        if (!empty($paymentType) && !in_array($paymentType, ['no', 'no_payment', '0'], true)) {
            $query->where('payment_instrument', $paymentType);
        }

        // Service filter (ignores 'no', '0', empty)
        $serviceId = $filters['service_id'] ?? null;
        if (!empty($serviceId) && !in_array($serviceId, ['no', '0'], true)) {
            $query->whereHas('bookingdetail', function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            });
        }

        // Category filter (ignores 'no', '0', empty)
        $categoryId = $filters['category_id'] ?? null;
        if (!empty($categoryId) && !in_array($categoryId, ['no', '0'], true)) {
            $query->whereHas('bookingdetail.service', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        return $query->orderBy($dateColumn, 'desc');
    }

    /**
     * Generate the structured report data array grouped by invoice.
     * Preserves 100% backward compatibility with reports_by_types.blade.php
     *
     * @return array<string|int, array> Empty array if no records found
     */
    public function getReportData(int $companyId, array $filters): array
    {
        $startDate = $this->normalizeDate($filters['start_date'] ?? null) ?? 'no_start_date';
        $endDate   = $this->normalizeDate($filters['end_date'] ?? null) ?? 'no_end_date';

        $bookings = $this->buildQuery($companyId, $filters, 'due_date')->get();

        if ($bookings->isEmpty()) {
            return [];
        }

        $groupedByInvoice = $bookings->groupBy('invoice');
        $dataArray = [];

        foreach ($groupedByInvoice as $invoiceKey => $bookingGroup) {
            if ($bookingGroup->isEmpty()) {
                continue;
            }

            $firstBooking = $bookingGroup->first();

            // Extract services across all details/bookings in this invoice group
            $servicesList = [];
            $seenServiceIds = [];

            foreach ($bookingGroup as $booking) {
                if ($booking->relationLoaded('services') && $booking->services->isNotEmpty()) {
                    foreach ($booking->services as $service) {
                        if (!empty($service->name) && !in_array($service->id, $seenServiceIds)) {
                            $seenServiceIds[] = $service->id;
                            $servicesList[] = [
                                'id'    => $service->id,
                                'name'  => $service->name,
                                'color' => $service->service_color ?? '#6c757d',
                            ];
                        }
                    }
                } elseif ($booking->relationLoaded('bookingdetail') && $booking->bookingdetail->isNotEmpty()) {
                    foreach ($booking->bookingdetail as $detail) {
                        if ($detail->service && !empty($detail->service->name) && !in_array($detail->service->id, $seenServiceIds)) {
                            $seenServiceIds[] = $detail->service->id;
                            $servicesList[] = [
                                'id'    => $detail->service->id,
                                'name'  => $detail->service->name,
                                'color' => $detail->service->service_color ?? '#6c757d',
                            ];
                        }
                    }
                }
            }

            $servicesString = implode(', ', array_column($servicesList, 'name'));

            $userName = $firstBooking->user ? $firstBooking->user->name : '';
            $customerName = $firstBooking->customer
                ? trim($firstBooking->customer->first_name . ' ' . $firstBooking->customer->last_name)
                : '';
            $address = $firstBooking->customer ? ($firstBooking->customer->address ?? '') : '';

            $row = [
                'user_name'       => $userName,
                'invoice'         => $firstBooking->invoice,
                'customer_names'  => $customerName,
                'total_price'     => $firstBooking->total_price,
                'payment_type'    => $firstBooking->payment_instrument,
                'due_date'        => $firstBooking->due_date,
                'address'         => $address,
                'booking_status'  => $firstBooking->service_status,
                'services'        => $servicesList,
            ];

            // Blade views expect numeric index 0 = services, 1 = start_date, 2 = end_date
            // via legacy array_push($data_array[$key], $services, $start_date, $end_date)
            $row[0] = $servicesString;
            $row[1] = $startDate;
            $row[2] = $endDate;

            $dataArray[$invoiceKey] = $row;
        }

        return $dataArray;
    }

    /**
     * Stream CSV export directly to output.
     * Highly memory efficient for large datasets (millions of users/records).
     */
    public function streamCsvExport(int $companyId, array $filters, ?string $filename = null)
    {
        $filename = $filename ?? (date('d-m-Y') . 'Reports.csv');

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $query = $this->buildQuery($companyId, $filters, 'due_date');

        $statusMap = [
            1 => 'Booked',
            2 => 'In-Progress',
            3 => 'Completed',
            4 => 'Cancel',
            5 => 'Survey',
        ];

        $callback = function () use ($query, $statusMap) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Customer Name',
                'User Name',
                'Services',
                'Total Price',
                'Due Date',
                'Due Time',
                'Status',
                'Payment Method',
            ]);

            // Stream in chunks of 500 records to keep memory footprint minimal
            $query->chunk(500, function ($bookings) use ($handle, $statusMap) {
                foreach ($bookings as $booking) {
                    $customerName = $booking->customer
                        ? trim($booking->customer->first_name . ' ' . $booking->customer->last_name)
                        : '';
                    $userName = $booking->user ? $booking->user->name : '';

                    $serviceNames = [];
                    if ($booking->services && $booking->services->isNotEmpty()) {
                        $serviceNames = $booking->services->pluck('name')->toArray();
                    } elseif ($booking->bookingdetail && $booking->bookingdetail->isNotEmpty()) {
                        foreach ($booking->bookingdetail as $detail) {
                            if ($detail->service) {
                                $serviceNames[] = $detail->service->name;
                            }
                        }
                    }
                    $servicesString = implode(', ', array_unique($serviceNames));

                    fputcsv($handle, [
                        $customerName,
                        $userName,
                        $servicesString,
                        $booking->total_price,
                        $booking->due_date,
                        $booking->due_time,
                        $statusMap[$booking->service_status] ?? $booking->service_status,
                        $booking->payment_instrument,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get Customer Booking Report records with eager loading (zero N+1 queries).
     */
    public function getCustomerReportData(int $companyId, array $filters): array
    {
        $customerId = $filters['customer_id'] ?? null;
        $startDate  = $filters['start_date'] ?? 'no-date';
        $endDate    = $filters['end_date'] ?? 'no-date';

        $query = ServiceBooking::with([
                'customer:id,first_name,last_name,address,zip',
                'user:id,name',
                'services',
                'bookingdetail.service:id,name,service_color'
            ])
            ->where('company_id', $companyId);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($startDate !== 'no-date' && $endDate !== 'no-date' && !empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        if ($bookings->isEmpty()) {
            return [
                'data_array'   => [],
                'total_amount' => 0,
            ];
        }

        $dataArray = [];
        $totalAmount = 0;

        foreach ($bookings as $key => $value) {
            $servicesList = [];
            $seenServiceIds = [];

            if ($value->services && $value->services->isNotEmpty()) {
                foreach ($value->services as $service) {
                    if (!empty($service->name) && !in_array($service->id, $seenServiceIds)) {
                        $seenServiceIds[] = $service->id;
                        $servicesList[] = [
                            'id'    => $service->id,
                            'name'  => $service->name,
                            'color' => $service->service_color ?? '#6c757d',
                        ];
                    }
                }
            } elseif ($value->bookingdetail && $value->bookingdetail->isNotEmpty()) {
                foreach ($value->bookingdetail as $detail) {
                    if ($detail->service && !empty($detail->service->name) && !in_array($detail->service->id, $seenServiceIds)) {
                        $seenServiceIds[] = $detail->service->id;
                        $servicesList[] = [
                            'id'    => $detail->service->id,
                            'name'  => $detail->service->name,
                            'color' => $detail->service->service_color ?? '#6c757d',
                        ];
                    }
                }
            }

            $servicesString = implode(', ', array_column($servicesList, 'name'));

            $totalAmount += $value->total_price;

            $dataArray[$key] = [
                'id'                  => $value->id,
                'status'              => $value->status,
                'invoice'              => $value->invoice,
                'customer_name'       => $value->customer ? trim($value->customer->first_name . ' ' . $value->customer->last_name) : '',
                'customer_address'    => $value->customer->address ?? '',
                'customer_zip'        => $value->customer->zip ?? '',
                'user_name'           => $value->user->name ?? '',
                'total_price'         => $value->total_price,
                'due_date'            => $value->due_date,
                'due_time'            => $value->due_time,
                'service_time'        => $value->service_time,
                'service_status'      => $value->service_status,
                'services'            => $servicesString,
                'services_with_color' => $servicesList,
                'payment_instrument'  => $value->payment_instrument,
                'payment_days'        => $value->payment_days,
            ];
        }

        return [
            'data_array'   => $dataArray,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Stream Customer Report CSV export.
     */
    public function streamCustomerCsvExport(int $companyId, $startDate, $endDate, $customerId)
    {
        $fileName = date('d-m-Y') . 'Customer Report.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $query = ServiceBooking::with([
                'customer:id,first_name,last_name,address',
                'user:id,name',
                'services',
                'bookingdetail.service:id,name'
            ])
            ->where('customer_id', $customerId)
            ->where('company_id', $companyId);

        if ($startDate !== 'no-date' && !empty($startDate)) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();
        $totalAmount = $bookings->sum(fn($b) => $b->total_price + $b->vat);

        $callback = function () use ($bookings, $startDate, $endDate, $totalAmount) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ["Start Date: $startDate, End Date: $endDate"]);
            fputcsv($file, ["Total-Amount: $totalAmount"]);
            fputcsv($file, [
                'Customer',
                'Customer Address',
                'Booking User',
                'Services',
                'Total Price',
                'Vat',
                'Booking Date',
                'Status',
                'Payment Days',
                'Payment Method'
            ]);

            $statusMap = [
                '1' => 'Booked',
                '2' => 'In Progress',
                '3' => 'Completed',
                '4' => 'Canceled'
            ];

            foreach ($bookings as $value) {
                $serviceNames = [];
                if ($value->services && $value->services->isNotEmpty()) {
                    $serviceNames = $value->services->pluck('name')->toArray();
                } elseif ($value->bookingdetail && $value->bookingdetail->isNotEmpty()) {
                    foreach ($value->bookingdetail as $detail) {
                        if ($detail->service) {
                            $serviceNames[] = $detail->service->name;
                        }
                    }
                }
                $services = implode(', ', array_unique($serviceNames));
                $customerName = $value->customer ? trim($value->customer->first_name . ' ' . $value->customer->last_name) : '';

                fputcsv($file, [
                    $customerName,
                    $value->customer->address ?? '',
                    $value->user->name ?? '',
                    $services,
                    $value->total_price,
                    $value->vat,
                    $value->due_date . ' Time: ' . $value->due_time,
                    $statusMap[$value->service_status] ?? $value->service_status,
                    $value->payment_days,
                    $value->payment_instrument,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

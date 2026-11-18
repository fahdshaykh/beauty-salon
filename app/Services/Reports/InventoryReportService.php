<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

class InventoryReportService
{
    /**
     * Build base query for inventory usage records.
     * Enforces strict multi-tenant company isolation with correct operator precedence.
     */
    public function buildQuery(int $companyId, array $filters)
    {
        $startDate   = $filters['start_date'] ?? null;
        $endDate     = $filters['end_date'] ?? null;
        $inventoryId = $filters['inventory_id'] ?? null;

        $query = DB::table('inventory_books as ib')
            ->join('inventories as inv', 'ib.inventory_id', '=', 'inv.id')
            ->join('service_bookings as sb', 'ib.booking_id', '=', 'sb.id')
            ->join('customers as c', 'sb.customer_id', '=', 'c.id')
            ->join('users as u', 'sb.user_id', '=', 'u.id')
            ->where('sb.company_id', $companyId)
            ->whereIn('sb.service_status', ['2', '3'])
            ->where('sb.service_status', '!=', 5);

        if ($startDate && $endDate) {
            $query->whereDate('sb.created_at', '>=', $startDate . ' 00:00:00')
                  ->whereDate('sb.created_at', '<=', $endDate . ' 23:59:59');
        }

        if (!empty($inventoryId) && $inventoryId !== 'no') {
            $query->where('ib.inventory_id', $inventoryId);
        }

        return $query->select([
            'ib.id as inventory_book_id',
            'inv.name as inventory_name',
            'ib.quantity',
            'ib.booking_id',
            'c.first_name',
            'c.last_name',
            'u.name as employee_name',
            'sb.due_date as booking_date',
            'sb.due_time as booking_time',
            'sb.invoice',
            'sb.total_price',
            'sb.service_status',
            'sb.created_at'
        ])->orderBy('sb.created_at', 'desc');
    }

    /**
     * Get structured inventory report data for view presentation.
     */
    public function getReportData(int $companyId, array $filters): array
    {
        $records = $this->buildQuery($companyId, $filters)->get();

        if ($records->isEmpty()) {
            return [
                'records' => [],
                'total_quantity_used' => 0
            ];
        }

        $dataArray = [];
        $totalQuantityUsed = 0;

        foreach ($records as $key => $record) {
            $totalQuantityUsed += (float)$record->quantity;

            $dataArray[$key] = [
                'inventory_book_id' => $record->inventory_book_id,
                'inventory_name'    => $record->inventory_name,
                'booking_id'        => $record->booking_id,
                'invoice'           => $record->invoice,
                'customer_name'     => trim($record->first_name . ' ' . $record->last_name),
                'employee_name'     => $record->employee_name,
                'quantity_used'     => number_format($record->quantity, 2),
                'booking_date'      => $record->booking_date,
                'booking_time'      => $record->booking_time,
                'total_price'       => $record->total_price,
                'service_status'    => $record->service_status,
            ];
        }

        return [
            'records' => $dataArray,
            'total_quantity_used' => $totalQuantityUsed
        ];
    }

    /**
     * Stream CSV export directly to output for large datasets.
     */
    public function streamCsvExport(int $companyId, ?string $startDate, ?string $endDate, $inventoryId)
    {
        $fileName = 'inventory_usage_report_' . ($startDate ?? 'all') . '_to_' . ($endDate ?? 'all') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $filters = [
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'inventory_id' => $inventoryId,
        ];

        $query = $this->buildQuery($companyId, $filters);

        $statusMap = [
            1 => 'Booked',
            2 => 'In-Progress',
            3 => 'Completed',
            4 => 'Cancelled'
        ];

        $callback = function () use ($query, $statusMap) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Inventory Name',
                'Quantity Used',
                'Booking ID',
                'Customer Name',
                'Employee Name',
                'Booking Date',
                'Booking Time',
                'Total Price',
                'Status'
            ]);

            // Stream in chunks of 500
            $query->chunk(500, function ($records) use ($handle, $statusMap) {
                foreach ($records as $row) {
                    $fullName = trim($row->first_name . ' ' . $row->last_name);
                    fputcsv($handle, [
                        $row->inventory_name,
                        $row->quantity,
                        $row->booking_id,
                        $fullName,
                        $row->employee_name,
                        $row->booking_date,
                        $row->booking_time,
                        $row->total_price,
                        $statusMap[$row->service_status] ?? 'Unknown'
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}


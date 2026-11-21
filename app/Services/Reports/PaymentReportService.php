<?php

namespace App\Services\Reports;

use App\Models\PaymentMethod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentReportService
{
    /**
     * Normalize an input date into standard Y-m-d format.
     */
    public function normalizeDate(?string $date): ?string
    {
        if (empty($date) || $date === 'no_start_date' || $date === 'no_end_date' || $date === 'no') {
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
     * Parse payment_details JSON or array safely.
     */
    public function parsePaymentDetails($details): array
    {
        if (empty($details)) {
            return [];
        }
        if (is_array($details)) {
            return $details;
        }
        if (is_string($details)) {
            $decoded = json_decode($details, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * Format payment details into a standardized array.
     */
    public function formatPaymentDetails($details, ?string $instrumentNumber = null): array
    {
        $parsed = $this->parsePaymentDetails($details);
        if (!empty($instrumentNumber) && empty($parsed['cheque_number']) && empty($parsed['instrument_number']) && empty($parsed['ref'])) {
            $parsed['instrument_number'] = $instrumentNumber;
        }
        return $parsed;
    }

    /**
     * Retrieve consolidated payment report data across bookings, expenses, and inventory purchases.
     */
    public function getReportData(int $companyId, array $filters): array
    {
        $startDate       = $this->normalizeDate($filters['start_date'] ?? null);
        $endDate         = $this->normalizeDate($filters['end_date'] ?? null);
        $rawMethod       = $filters['payment_method'] ?? 'all';
        $transactionType = $filters['transaction_type'] ?? 'all';
        $flowType        = $filters['flow_type'] ?? 'all'; // all, inflow, outflow

        $methodName = null;
        $methodId   = null;

        if (!empty($rawMethod) && $rawMethod !== 'all' && $rawMethod !== 'no') {
            if (is_numeric($rawMethod)) {
                $methodId  = (int)$rawMethod;
                $methodObj = PaymentMethod::find($methodId);
                if ($methodObj) {
                    $methodName = strtolower(trim($methodObj->method));
                }
            } else {
                $methodName = strtolower(trim($rawMethod));
                $methodObj  = PaymentMethod::where('company_id', $companyId)
                    ->whereRaw('LOWER(method) = ?', [$methodName])
                    ->first();
                if ($methodObj) {
                    $methodId = $methodObj->id;
                }
            }
        }

        $records = collect();

        // 1. Service Bookings (Inflow)
        if ($flowType !== 'outflow' && ($transactionType === 'all' || $transactionType === 'booking')) {
            $bookingsQuery = DB::table('service_bookings')
                ->leftJoin('customers', 'customers.id', '=', 'service_bookings.customer_id')
                ->where('service_bookings.company_id', $companyId)
                ->where('service_bookings.is_deleted', 0)
                ->where('service_bookings.service_status', 3) // Completed bookings
                ->select(
                    'service_bookings.id',
                    'service_bookings.invoice',
                    'service_bookings.due_date as date',
                    'service_bookings.due_time',
                    'service_bookings.total_price as amount',
                    'service_bookings.payment_instrument as payment_method',
                    'service_bookings.payment_instrument_number',
                    'service_bookings.payment_details',
                    'customers.first_name',
                    'customers.last_name',
                    'customers.primary_phone'
                );

            if ($startDate && $endDate) {
                $bookingsQuery->whereBetween('service_bookings.due_date', [$startDate, $endDate]);
            } elseif ($startDate) {
                $bookingsQuery->where('service_bookings.due_date', '>=', $startDate);
            } elseif ($endDate) {
                $bookingsQuery->where('service_bookings.due_date', '<=', $endDate);
            }

            if ($methodName) {
                $bookingsQuery->where(function ($q) use ($methodName, $methodId) {
                    $q->whereRaw('LOWER(service_bookings.payment_instrument) = ?', [$methodName]);
                    if ($methodId) {
                        $q->orWhere('service_bookings.payment_instrument', (string)$methodId);
                    }
                });
            }

            $bookings = $bookingsQuery->get();

            foreach ($bookings as $b) {
                $partyName = trim(($b->first_name ?? '') . ' ' . ($b->last_name ?? ''));
                $records->push([
                    'id'               => $b->id,
                    'source_key'       => 'booking',
                    'source_label'     => 'Service Booking',
                    'source_badge'     => 'badge-success',
                    'flow'             => 'inflow',
                    'date'             => $b->date ?: date('Y-m-d'),
                    'time'             => $b->due_time,
                    'reference'        => $b->invoice ?: ('#SB-' . $b->id),
                    'party'            => $partyName ?: 'Customer',
                    'party_phone'      => $b->primary_phone ?? '',
                    'description'      => 'Customer Appointment Service',
                    'payment_method'   => ucfirst($b->payment_method ?: 'Cash'),
                    'payment_details'  => $this->formatPaymentDetails($b->payment_details, $b->payment_instrument_number),
                    'inflow_amount'    => (float)$b->amount,
                    'outflow_amount'   => 0.00,
                ]);
            }
        }

        // 2. Expenses (Outflow)
        if ($flowType !== 'inflow' && ($transactionType === 'all' || $transactionType === 'expense')) {
            $expensesQuery = DB::table('expenses')
                ->leftJoin('expense_types', 'expense_types.id', '=', 'expenses.expense_type')
                ->leftJoin('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method')
                ->where('expenses.company_id', $companyId)
                ->select(
                    'expenses.id',
                    DB::raw('COALESCE(expenses.payable, DATE(expenses.created_at)) as date'),
                    'expenses.amount',
                    'expenses.description',
                    'expenses.payment_method as raw_payment_method',
                    'payment_methods.method as payment_method',
                    'expenses.payment_details',
                    'expenses.payment_file',
                    'expense_types.type as expense_type'
                );

            if ($startDate && $endDate) {
                $expensesQuery->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('expenses.payable', [$startDate, $endDate])
                      ->orWhere(function ($sub) use ($startDate, $endDate) {
                          $sub->whereNull('expenses.payable')
                              ->whereBetween(DB::raw('DATE(expenses.created_at)'), [$startDate, $endDate]);
                      });
                });
            } elseif ($startDate) {
                $expensesQuery->where(function ($q) use ($startDate) {
                    $q->where('expenses.payable', '>=', $startDate)
                      ->orWhere(function ($sub) use ($startDate) {
                          $sub->whereNull('expenses.payable')
                              ->whereDate('expenses.created_at', '>=', $startDate);
                      });
                });
            } elseif ($endDate) {
                $expensesQuery->where(function ($q) use ($endDate) {
                    $q->where('expenses.payable', '<=', $endDate)
                      ->orWhere(function ($sub) use ($endDate) {
                          $sub->whereNull('expenses.payable')
                              ->whereDate('expenses.created_at', '<=', $endDate);
                      });
                });
            }

            if ($methodName) {
                $expensesQuery->where(function ($q) use ($methodName, $methodId) {
                    if ($methodId) {
                        $q->where('expenses.payment_method', $methodId);
                    }
                    $q->orWhereRaw('LOWER(payment_methods.method) = ?', [$methodName]);
                });
            }

            $expenses = $expensesQuery->get();

            foreach ($expenses as $e) {
                $records->push([
                    'id'               => $e->id,
                    'source_key'       => 'expense',
                    'source_label'     => 'Expense',
                    'source_badge'     => 'badge-danger',
                    'flow'             => 'outflow',
                    'date'             => $e->date ?: date('Y-m-d'),
                    'time'             => null,
                    'reference'        => $e->expense_type ?: ('Expense #' . $e->id),
                    'party'            => $e->description ?: ($e->expense_type ?? 'Operating Expense'),
                    'party_phone'      => '',
                    'description'      => $e->description ?: ($e->expense_type ?? ''),
                    'payment_method'   => ucfirst($e->payment_method ?: ($e->raw_payment_method ?? 'Cash')),
                    'payment_details'  => $this->formatPaymentDetails($e->payment_details),
                    'payment_file'     => $e->payment_file ?? null,
                    'inflow_amount'    => 0.00,
                    'outflow_amount'   => (float)$e->amount,
                ]);
            }
        }

        // 3. Inventory Purchases (Outflow)
        if ($flowType !== 'inflow' && ($transactionType === 'all' || $transactionType === 'inventory_purchase')) {
            $purchasesQuery = DB::table('inventories_purchases')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'inventories_purchases.supplier_id')
                ->leftJoin('inventories', 'inventories.id', '=', 'inventories_purchases.inventory_id')
                ->where('inventories_purchases.company_id', $companyId)
                ->where('inventories_purchases.amount_paid', '>', 0)
                ->select(
                    'inventories_purchases.id',
                    DB::raw('COALESCE(inventories_purchases.purchase_date, DATE(inventories_purchases.created_at)) as date'),
                    'inventories_purchases.amount_paid as amount',
                    'inventories_purchases.description',
                    'inventories_purchases.payment_type as payment_method',
                    'inventories_purchases.payment_details',
                    DB::raw('COALESCE(inventories_purchases.payment_file, inventories_purchases.image) as payment_file'),
                    'suppliers.name as supplier_name',
                    'suppliers.phone as supplier_phone',
                    'inventories.name as inventory_name'
                );

            if ($startDate && $endDate) {
                $purchasesQuery->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('inventories_purchases.purchase_date', [$startDate, $endDate])
                      ->orWhere(function ($sub) use ($startDate, $endDate) {
                          $sub->whereNull('inventories_purchases.purchase_date')
                              ->whereBetween(DB::raw('DATE(inventories_purchases.created_at)'), [$startDate, $endDate]);
                      });
                });
            } elseif ($startDate) {
                $purchasesQuery->where(function ($q) use ($startDate) {
                    $q->where('inventories_purchases.purchase_date', '>=', $startDate)
                      ->orWhere(function ($sub) use ($startDate) {
                          $sub->whereNull('inventories_purchases.purchase_date')
                              ->whereDate('inventories_purchases.created_at', '>=', $startDate);
                      });
                });
            } elseif ($endDate) {
                $purchasesQuery->where(function ($q) use ($endDate) {
                    $q->where('inventories_purchases.purchase_date', '<=', $endDate)
                      ->orWhere(function ($sub) use ($endDate) {
                          $sub->whereNull('inventories_purchases.purchase_date')
                              ->whereDate('inventories_purchases.created_at', '<=', $endDate);
                      });
                });
            }

            if ($methodName) {
                $purchasesQuery->where(function ($q) use ($methodName, $methodId) {
                    $q->whereRaw('LOWER(inventories_purchases.payment_type) = ?', [$methodName]);
                    if ($methodId) {
                        $q->orWhere('inventories_purchases.payment_type', (string)$methodId);
                    }
                });
            }

            $purchases = $purchasesQuery->get();

            foreach ($purchases as $p) {
                $supplierName = $p->supplier_name ?: 'Supplier';
                $records->push([
                    'id'               => $p->id,
                    'source_key'       => 'inventory_purchase',
                    'source_label'     => 'Inventory Purchase',
                    'source_badge'     => 'badge-warning',
                    'flow'             => 'outflow',
                    'date'             => $p->date ?: date('Y-m-d'),
                    'time'             => null,
                    'reference'        => 'PO #' . $p->id . ($p->inventory_name ? ' (' . $p->inventory_name . ')' : ''),
                    'party'            => $supplierName,
                    'party_phone'      => $p->supplier_phone ?? '',
                    'description'      => $p->description ?: ('Purchased ' . ($p->inventory_name ?? 'inventory items')),
                    'payment_method'   => ucfirst($p->payment_method ?: 'Cash'),
                    'payment_details'  => $this->formatPaymentDetails($p->payment_details),
                    'payment_file'     => $p->payment_file ?? null,
                    'inflow_amount'    => 0.00,
                    'outflow_amount'   => (float)$p->amount,
                ]);
            }
        }

        // 4. Direct Supplier Balance Payments (Outflow)
        if ($flowType !== 'inflow' && ($transactionType === 'all' || $transactionType === 'supplier_payment')) {
            $spQuery = DB::table('supplier_payments')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_payments.supplier_id')
                ->where('supplier_payments.company_id', $companyId)
                ->whereNull('supplier_payments.inventory_purchase_id')
                ->where('supplier_payments.amount', '>', 0)
                ->select(
                    'supplier_payments.id',
                    DB::raw('DATE(supplier_payments.created_at) as date'),
                    'supplier_payments.amount',
                    'supplier_payments.note as description',
                    'supplier_payments.payment_method',
                    'supplier_payments.payment_details',
                    'supplier_payments.payment_file',
                    'suppliers.name as supplier_name',
                    'suppliers.phone as supplier_phone'
                );

            if ($startDate && $endDate) {
                $spQuery->whereBetween(DB::raw('DATE(supplier_payments.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $spQuery->whereDate('supplier_payments.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $spQuery->whereDate('supplier_payments.created_at', '<=', $endDate);
            }

            if ($methodName) {
                $spQuery->where(function ($q) use ($methodName, $methodId) {
                    $q->whereRaw('LOWER(supplier_payments.payment_method) = ?', [$methodName]);
                    if ($methodId) {
                        $q->orWhere('supplier_payments.payment_method', (string)$methodId);
                    }
                });
            }

            $spRecords = $spQuery->get();

            foreach ($spRecords as $sp) {
                $supplierName = $sp->supplier_name ?: 'Supplier';
                $records->push([
                    'id'               => $sp->id,
                    'source_key'       => 'supplier_payment',
                    'source_label'     => 'Supplier Payment',
                    'source_badge'     => 'badge-info',
                    'flow'             => 'outflow',
                    'date'             => $sp->date ?: date('Y-m-d'),
                    'time'             => null,
                    'reference'        => 'PAY-' . str_pad($sp->id, 5, '0', STR_PAD_LEFT),
                    'party'            => $supplierName,
                    'party_phone'      => $sp->supplier_phone ?? '',
                    'description'      => $sp->description ?: ('Direct Balance Payment to ' . $supplierName),
                    'payment_method'   => ucfirst($sp->payment_method ?: 'Cash'),
                    'payment_details'  => $this->formatPaymentDetails($sp->payment_details),
                    'payment_file'     => $sp->payment_file ?? null,
                    'inflow_amount'    => 0.00,
                    'outflow_amount'   => (float)$sp->amount,
                ]);
            }
        }

        // Sort records chronologically descending
        $sortedRecords = $records->sort(function ($a, $b) {
            $cmp = strcmp($b['date'], $a['date']);
            if ($cmp === 0) {
                return $b['id'] <=> $a['id'];
            }
            return $cmp;
        })->values();

        // Calculate aggregate summaries
        $totalInflow  = (float)$sortedRecords->sum('inflow_amount');
        $totalOutflow = (float)$sortedRecords->sum('outflow_amount');
        $netBalance   = $totalInflow - $totalOutflow;

        $byMethod = [];
        foreach ($sortedRecords as $r) {
            $m = $r['payment_method'] ?: 'Other';
            if (!isset($byMethod[$m])) {
                $byMethod[$m] = [
                    'name'    => $m,
                    'inflow'  => 0.0,
                    'outflow' => 0.0,
                    'net'     => 0.0,
                    'count'   => 0,
                ];
            }
            $byMethod[$m]['inflow']  += $r['inflow_amount'];
            $byMethod[$m]['outflow'] += $r['outflow_amount'];
            $byMethod[$m]['net']     += ($r['inflow_amount'] - $r['outflow_amount']);
            $byMethod[$m]['count']   += 1;
        }

        ksort($byMethod);

        return [
            'records'       => $sortedRecords,
            'total_inflow'  => $totalInflow,
            'total_outflow' => $totalOutflow,
            'net_balance'   => $netBalance,
            'by_method'     => $byMethod,
            'counts'        => [
                'total'   => $sortedRecords->count(),
                'inflow'  => $sortedRecords->where('flow', 'inflow')->count(),
                'outflow' => $sortedRecords->where('flow', 'outflow')->count(),
            ],
            'start_date'    => $startDate,
            'end_date'      => $endDate,
        ];
    }

    /**
     * Stream CSV export directly to the browser for download.
     */
    public function streamCsvExport(int $companyId, array $filters): StreamedResponse
    {
        $reportData = $this->getReportData($companyId, $filters);
        $records    = $reportData['records'];

        $startDate = $reportData['start_date'] ?: 'All Dates';
        $endDate   = $reportData['end_date'] ?: 'All Dates';
        $fileName  = 'Payment-Report-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($records, $reportData, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // Metadata rows
            fputcsv($file, ['PAYMENT REPORT']);
            fputcsv($file, ['Date Range:', $startDate . ' to ' . $endDate]);
            fputcsv($file, ['Total Inflow (Received):', number_format($reportData['total_inflow'], 2)]);
            fputcsv($file, ['Total Outflow (Paid):', number_format($reportData['total_outflow'], 2)]);
            fputcsv($file, ['Net Balance:', number_format($reportData['net_balance'], 2)]);
            fputcsv($file, []); // Empty line

            // Payment Methods Summary Breakdown
            fputcsv($file, ['PAYMENT METHOD SUMMARY BREAKDOWN']);
            fputcsv($file, ['Method', 'Transactions Count', 'Inflow (+)', 'Outflow (-)', 'Net Impact']);
            foreach ($reportData['by_method'] as $m) {
                fputcsv($file, [
                    $m['name'],
                    $m['count'],
                    number_format($m['inflow'], 2),
                    number_format($m['outflow'], 2),
                    number_format($m['net'], 2),
                ]);
            }
            fputcsv($file, []); // Empty line

            // Main Data Table Headers
            fputcsv($file, [
                '#',
                'Date',
                'Source / Module',
                'Flow',
                'Reference / Invoice',
                'Party / Payee / Customer',
                'Phone',
                'Payment Method',
                'Custom Payment Details',
                'Inflow Amount (+)',
                'Outflow Amount (-)',
                'Description / Notes'
            ]);

            $index = 1;
            foreach ($records as $r) {
                $detailsStr = '';
                if (!empty($r['payment_details']) && is_array($r['payment_details'])) {
                    $parts = [];
                    foreach ($r['payment_details'] as $k => $v) {
                        if ($v !== null && $v !== '') {
                            $parts[] = ucwords(str_replace('_', ' ', $k)) . ': ' . $v;
                        }
                    }
                    $detailsStr = implode(' | ', $parts);
                }

                fputcsv($file, [
                    $index++,
                    $r['date'],
                    $r['source_label'],
                    strtoupper($r['flow']),
                    $r['reference'],
                    $r['party'],
                    $r['party_phone'],
                    $r['payment_method'],
                    $detailsStr,
                    $r['inflow_amount'] > 0 ? number_format($r['inflow_amount'], 2) : '0.00',
                    $r['outflow_amount'] > 0 ? number_format($r['outflow_amount'], 2) : '0.00',
                    $r['description']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace App\Services\Reports;

use App\Expense;
use App\Models\SupplierPayment;
use App\Models\Payroll;
use App\ServiceBooking;
use Carbon\Carbon;

class ProfitLossReportService
{
    /**
     * Compute the profit & loss metrics for a company within a date range.
     */
    public function getProfitLossData(int $companyId, ?string $rawStartDate, ?string $rawEndDate): array
    {
        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate   = now()->format('Y-m-d');

        if (!empty($rawStartDate) && !empty($rawEndDate)) {
            $startDate = date('Y-m-d', strtotime($rawStartDate));
            $endDate   = date('Y-m-d', strtotime($rawEndDate));
        }

        // 1. General Expenses
        $expenses = Expense::where('company_id', $companyId)
            ->whereDate('payable', '>=', $startDate)
            ->whereDate('payable', '<=', $endDate)
            ->sum('amount');

        // 2. Supplier Payments
        $supplier = SupplierPayment::where('company_id', $companyId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('amount');

        // 3. Staff Salaries Paid (net_pay)
        $salaries = Payroll::whereHas('user', fn($q) => $q->where('company_id', $companyId))
            ->whereDate('pay_period_start', '>=', $startDate)
            ->whereDate('pay_period_end', '<=', $endDate)
            ->sum('net_pay');

        // 4. Sales (Completed Bookings)
        $sales = ServiceBooking::where('company_id', $companyId)
            ->where('service_status', 3)
            ->whereDate('due_date', '>=', $startDate)
            ->whereDate('due_date', '<=', $endDate)
            ->sum('total_price');

        // 5. Aggregations
        $totalExpenses = $expenses + $supplier + $salaries;
        $profit = $sales - $totalExpenses;

        return [
            'expense'   => round($totalExpenses, 2),
            'sales'     => round($sales, 2),
            'revenue'   => round($profit, 2),
            'breakdown' => [
                'general_expenses'  => round($expenses, 2),
                'supplier_payments' => round($supplier, 2),
                'salaries_paid'     => round($salaries, 2),
            ]
        ];
    }
}


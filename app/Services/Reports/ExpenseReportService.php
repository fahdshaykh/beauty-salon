<?php

namespace App\Services\Reports;

use App\Expense;

class ExpenseReportService
{
    /**
     * Build base query for expense records.
     */
    public function buildQuery(int $companyId, array $filters)
    {
        $startDate     = $filters['start_date'] ?? null;
        $endDate       = $filters['end_date'] ?? null;
        $expenseType   = $filters['expense_type'] ?? null;
        $paymentMethod = $filters['payment_method'] ?? null;

        $query = Expense::select('expenses.*', 'expense_types.type', 'payment_methods.method')
            ->join('expense_types', 'expense_types.id', '=', 'expenses.expense_type')
            ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method')
            ->where('expenses.company_id', $companyId);

        if (!empty($startDate) && !empty($endDate) && $startDate !== 'no_start_date' && $endDate !== 'no_end_date') {
            $query->whereBetween('expenses.payable', [$startDate, $endDate]);
        }

        if (!empty($expenseType) && $expenseType !== 'no') {
            $query->where('expenses.expense_type', $expenseType);
        }

        if (!empty($paymentMethod) && $paymentMethod !== 'no') {
            $query->where('expenses.payment_method', $paymentMethod);
        }

        return $query->orderBy('expenses.created_at', 'desc');
    }

    /**
     * Fetch records for the expense report view.
     */
    public function getReportData(int $companyId, array $filters)
    {
        return $this->buildQuery($companyId, $filters)->get();
    }

    /**
     * Stream CSV export directly to output for large datasets.
     */
    public function streamCsvExport(int $companyId, array $filters)
    {
        $startDate = $filters['start_date'] ?? 'no_start_date';
        $endDate   = $filters['end_date'] ?? 'no_end_date';

        $fileName = date('d-m-Y') . 'Expense-Report.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $records = $this->buildQuery($companyId, $filters)->get();
        $totalAmount = $records->sum('amount');

        $callback = function () use ($records, $totalAmount, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ["Start Date: " . $startDate . ", End Date: " . $endDate]);
            fputcsv($file, ["Total-Amount: " . $totalAmount]);
            fputcsv($file, ['Expense Type', 'Amount', 'Payment Method', 'Date']);

            foreach ($records as $expense) {
                fputcsv($file, [
                    $expense->type,
                    $expense->amount,
                    $expense->method,
                    $expense->payable,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}


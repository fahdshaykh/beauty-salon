<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('view suppliers'))) {
            abort(403);
        }

        // get rows per page if passed with request
        if (request('rows')) {

            $rows = 500 < intval(request('rows')) ? 500 : intval(request('rows'));
            $rows = 5 > $rows ? 5 : $rows;
            Auth::user()->setSettings(['suppliers_rows_per_page' => $rows]);
        }
        // otherwise retrieve from user's settings
        $rows = Auth::user()->getSetting('suppliers_rows_per_page', auth()->user()->default_rows);

        // Build query
        $query = Supplier::where('company_id', auth()->user()->company_id);

        // Search by name or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by created_at date range (common for suppliers)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Execute with pagination + preserve filters in URL
        $suppliers = $query->latest()->paginate($rows);

        return view('suppliers.index', compact('suppliers', 'rows'));
    }

    public function create()
    {
        if (! (auth()->user() && auth()->user()->can('create suppliers'))) {
            abort(403);
        }

        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('create suppliers'))) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        Supplier::create($data);
        return back()->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        if ($supplier->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        if (! (auth()->user() && auth()->user()->can('edit suppliers'))) {
            abort(403);
        }

        if ($supplier->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        if (! (auth()->user() && auth()->user()->can('edit suppliers'))) {
            abort(403);
        }

        if ($supplier->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;

        $supplier->update($data);
        return back()->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if (! (auth()->user() && auth()->user()->can('delete suppliers'))) {
            abort(403);
        }

        if ($supplier->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $supplier->delete();
        return back()->with('destroy', 'Supplier deleted successfully.');
    }

    public function balanceSummary(Request $request, $supplierId)
    {
        if (! (auth()->user() && auth()->user()->can('supplier summary'))) {
            abort(403);
        }

        $companyId = auth()->user()->company_id;
        $supplier = Supplier::where('id', $supplierId)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if (!empty($startDate) && empty($endDate)) {
            $endDate = date('Y-m-d');
        }
        if (empty($startDate) && !empty($endDate)) {
            $startDate = '2000-01-01';
        }
        $hasDateFilter = !empty($startDate) && !empty($endDate);

        // 1) Lifetime totals computed in database engine (zero RAM overhead)
        $lifetimePurchases = (float) $supplier->purchases()->sum('price');
        $lifetimePaid = (float) $supplier->payments()->sum('amount');
        $lifetimeBalance = $lifetimePurchases - $lifetimePaid;

        // 2) Period & Opening Balance calculations
        if ($hasDateFilter) {
            $purchasesBefore = (float) $supplier->purchases()
                ->whereDate(DB::raw('COALESCE(purchase_date, DATE(created_at))'), '<', $startDate)
                ->sum('price');

            $paymentsBefore = (float) $supplier->payments()
                ->whereDate('created_at', '<', $startDate)
                ->sum('amount');

            $openingBalance = $purchasesBefore - $paymentsBefore;

            $periodPurchases = (float) $supplier->purchases()
                ->whereBetween(DB::raw('COALESCE(purchase_date, DATE(created_at))'), [$startDate, $endDate])
                ->sum('price');

            $periodPaid = (float) $supplier->payments()
                ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
                ->sum('amount');

            $closingBalance = $openingBalance + $periodPurchases - $periodPaid;
        } else {
            $openingBalance = 0.0;
            $periodPurchases = $lifetimePurchases;
            $periodPaid = $lifetimePaid;
            $closingBalance = $lifetimeBalance;
        }

        // 3) Unified Ledger Query (Purchases + Payments with Running Balance window function)
        $purchasesQuery = DB::table('inventories_purchases as ip')
            ->leftJoin('inventories as inv', 'inv.id', '=', 'ip.inventory_id')
            ->where('ip.supplier_id', $supplierId)
            ->where('ip.company_id', $companyId)
            ->select([
                DB::raw("'purchase' as txn_type"),
                'ip.id as txn_id',
                DB::raw('COALESCE(ip.purchase_date, DATE(ip.created_at)) as txn_date'),
                DB::raw("CONCAT('Bill #', ip.id, IF(inv.name IS NOT NULL, CONCAT(' - ', inv.name), '')) as reference"),
                'ip.description as note',
                DB::raw('CAST(ip.price AS DECIMAL(12,2)) as debit'),
                DB::raw('CAST(0.00 AS DECIMAL(12,2)) as credit'),
                'ip.created_at as created_timestamp'
            ]);

        if ($hasDateFilter) {
            $purchasesQuery->whereBetween(DB::raw('COALESCE(ip.purchase_date, DATE(ip.created_at))'), [$startDate, $endDate]);
        }

        $paymentsQuery = DB::table('supplier_payments as sp')
            ->where('sp.supplier_id', $supplierId)
            ->where('sp.company_id', $companyId)
            ->select([
                DB::raw("'payment' as txn_type"),
                'sp.id as txn_id',
                DB::raw('DATE(sp.created_at) as txn_date'),
                DB::raw("CONCAT('Payment #', sp.id, IF(sp.inventory_purchase_id IS NOT NULL, CONCAT(' (Bill #', sp.inventory_purchase_id, ')'), '')) as reference"),
                'sp.note as note',
                DB::raw('CAST(0.00 AS DECIMAL(12,2)) as debit'),
                DB::raw('CAST(sp.amount AS DECIMAL(12,2)) as credit'),
                'sp.created_at as created_timestamp'
            ]);

        if ($hasDateFilter) {
            $paymentsQuery->whereBetween(DB::raw('DATE(sp.created_at)'), [$startDate, $endDate]);
        }

        $unionQuery = $purchasesQuery->unionAll($paymentsQuery);

        $ledgerQuery = DB::query()->fromSub($unionQuery, 'ledger')
            ->select([
                'txn_type',
                'txn_id',
                'txn_date',
                'reference',
                'note',
                'debit',
                'credit',
                'created_timestamp',
                DB::raw("($openingBalance + SUM(debit - credit) OVER (ORDER BY txn_date ASC, created_timestamp ASC, txn_id ASC)) as running_balance")
            ])
            ->orderBy('txn_date', 'asc')
            ->orderBy('created_timestamp', 'asc')
            ->orderBy('txn_id', 'asc');

        $ledger = $ledgerQuery->paginate(25, ['*'], 'ledger_page')->appends($request->query());

        // 4) Tab 2: Purchases List (Paginated)
        $purchasesListQuery = $supplier->purchases()
            ->with('inventory')
            ->orderBy(DB::raw('COALESCE(purchase_date, created_at)'), 'desc');

        if ($hasDateFilter) {
            $purchasesListQuery->whereBetween(DB::raw('COALESCE(purchase_date, DATE(created_at))'), [$startDate, $endDate]);
        }
        $purchases = $purchasesListQuery->paginate(25, ['*'], 'purchases_page')->appends($request->query());

        // 5) Tab 3: Payments List (Paginated)
        $paymentsListQuery = $supplier->payments()
            ->with('purchase.inventory')
            ->orderBy('created_at', 'desc');

        if ($hasDateFilter) {
            $paymentsListQuery->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        }
        $payments = $paymentsListQuery->paginate(25, ['*'], 'payments_page')->appends($request->query());

        $activeTab = $request->input('tab', 'ledger');

        return view('suppliers.balance_summary', compact(
            'supplier',
            'openingBalance',
            'periodPurchases',
            'periodPaid',
            'closingBalance',
            'lifetimePurchases',
            'lifetimePaid',
            'lifetimeBalance',
            'hasDateFilter',
            'startDate',
            'endDate',
            'ledger',
            'purchases',
            'payments',
            'activeTab'
        ));
    }

    public function exportBalanceSummary(Request $request, $supplierId)
    {
        if (! (auth()->user() && auth()->user()->can('supplier summary'))) {
            abort(403);
        }

        $companyId = auth()->user()->company_id;
        $supplier = Supplier::where('id', $supplierId)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if (!empty($startDate) && empty($endDate)) {
            $endDate = date('Y-m-d');
        }
        if (empty($startDate) && !empty($endDate)) {
            $startDate = '2000-01-01';
        }
        $hasDateFilter = !empty($startDate) && !empty($endDate);

        if ($hasDateFilter) {
            $purchasesBefore = (float) $supplier->purchases()
                ->whereDate(DB::raw('COALESCE(purchase_date, DATE(created_at))'), '<', $startDate)
                ->sum('price');

            $paymentsBefore = (float) $supplier->payments()
                ->whereDate('created_at', '<', $startDate)
                ->sum('amount');

            $openingBalance = $purchasesBefore - $paymentsBefore;
        } else {
            $openingBalance = 0.0;
        }

        $purchasesQuery = DB::table('inventories_purchases as ip')
            ->leftJoin('inventories as inv', 'inv.id', '=', 'ip.inventory_id')
            ->where('ip.supplier_id', $supplierId)
            ->where('ip.company_id', $companyId)
            ->select([
                DB::raw("'purchase' as txn_type"),
                'ip.id as txn_id',
                DB::raw('COALESCE(ip.purchase_date, DATE(ip.created_at)) as txn_date'),
                DB::raw("CONCAT('Bill #', ip.id, IF(inv.name IS NOT NULL, CONCAT(' - ', inv.name), '')) as reference"),
                'ip.description as note',
                DB::raw('CAST(ip.price AS DECIMAL(12,2)) as debit'),
                DB::raw('CAST(0.00 AS DECIMAL(12,2)) as credit'),
                'ip.created_at as created_timestamp'
            ]);

        if ($hasDateFilter) {
            $purchasesQuery->whereBetween(DB::raw('COALESCE(ip.purchase_date, DATE(ip.created_at))'), [$startDate, $endDate]);
        }

        $paymentsQuery = DB::table('supplier_payments as sp')
            ->where('sp.supplier_id', $supplierId)
            ->where('sp.company_id', $companyId)
            ->select([
                DB::raw("'payment' as txn_type"),
                'sp.id as txn_id',
                DB::raw('DATE(sp.created_at) as txn_date'),
                DB::raw("CONCAT('Payment #', sp.id, IF(sp.inventory_purchase_id IS NOT NULL, CONCAT(' (Bill #', sp.inventory_purchase_id, ')'), '')) as reference"),
                'sp.note as note',
                DB::raw('CAST(0.00 AS DECIMAL(12,2)) as debit'),
                DB::raw('CAST(sp.amount AS DECIMAL(12,2)) as credit'),
                'sp.created_at as created_timestamp'
            ]);

        if ($hasDateFilter) {
            $paymentsQuery->whereBetween(DB::raw('DATE(sp.created_at)'), [$startDate, $endDate]);
        }

        $unionQuery = $purchasesQuery->unionAll($paymentsQuery);

        $ledgerQuery = DB::query()->fromSub($unionQuery, 'ledger')
            ->select([
                'txn_type',
                'txn_id',
                'txn_date',
                'reference',
                'note',
                'debit',
                'credit',
                'created_timestamp',
                DB::raw("($openingBalance + SUM(debit - credit) OVER (ORDER BY txn_date ASC, created_timestamp ASC, txn_id ASC)) as running_balance")
            ])
            ->orderBy('txn_date', 'asc')
            ->orderBy('created_timestamp', 'asc')
            ->orderBy('txn_id', 'asc');

        $filename = 'supplier_statement_' . $supplier->id . '_' . date('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($ledgerQuery, $supplier, $openingBalance, $startDate, $endDate, $hasDateFilter) {
            $handle = fopen('php://output', 'w');

            // Header metadata
            fputcsv($handle, ['SUPPLIER STATEMENT OF ACCOUNT']);
            fputcsv($handle, ['Supplier:', $supplier->name]);
            fputcsv($handle, ['Email:', $supplier->email ?? 'N/A', 'Phone:', $supplier->phone ?? 'N/A']);
            fputcsv($handle, ['Period:', $hasDateFilter ? ($startDate . ' to ' . $endDate) : 'All Time']);
            fputcsv($handle, ['Generated At:', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            // Column Headers
            fputcsv($handle, ['Date', 'Type', 'Reference', 'Description / Note', 'Billed (Debit)', 'Paid (Credit)', 'Running Balance']);

            // Opening Balance row
            fputcsv($handle, [
                $hasDateFilter ? $startDate : '-',
                'Opening Balance',
                'Balance Brought Forward',
                '-',
                '',
                '',
                number_format($openingBalance, 2, '.', '')
            ]);

            $totalDebit = 0;
            $totalCredit = 0;
            $lastBalance = $openingBalance;

            foreach ($ledgerQuery->cursor() as $row) {
                $totalDebit += (float) $row->debit;
                $totalCredit += (float) $row->credit;
                $lastBalance = (float) $row->running_balance;

                fputcsv($handle, [
                    $row->txn_date,
                    ucfirst($row->txn_type),
                    $row->reference,
                    $row->note ?? '-',
                    $row->debit > 0 ? number_format($row->debit, 2, '.', '') : '',
                    $row->credit > 0 ? number_format($row->credit, 2, '.', '') : '',
                    number_format($row->running_balance, 2, '.', '')
                ]);
            }

            // Summary row
            fputcsv($handle, []);
            fputcsv($handle, [
                'TOTALS',
                '',
                '',
                'Period Activity & Closing Balance',
                number_format($totalDebit, 2, '.', ''),
                number_format($totalCredit, 2, '.', ''),
                number_format($lastBalance, 2, '.', '')
            ]);

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}

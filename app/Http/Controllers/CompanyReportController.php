<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Expense;
use App\InventoryRemain;
use App\Services\Reports\BookingReportService;

class CompanyReportController extends Controller
{
    public function __construct(
        protected BookingReportService $bookingReportService
    ) {}

    public function company_graph_report()
    {
        return view('company_reports.graph_report');
    }

    public function company_booking_chart(Request $request)
    {
        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-t');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate   = date('Y-m-d', strtotime($request->end_date));
        }

        $company_summary = DB::table('service_bookings')
            ->join('companies', 'service_bookings.company_id', '=', 'companies.id')
            ->whereDate('service_bookings.due_date', '>=', $startDate)
            ->whereDate('service_bookings.due_date', '<=', $endDate)
            ->whereNotIn('service_bookings.service_status', [4, 5])
            ->select(
                DB::raw('COUNT(service_bookings.id) as total_bookings'),
                DB::raw('SUM(service_bookings.total_price) as total_amount'),
                'companies.company_name as company_name'
            )
            ->groupBy('service_bookings.company_id', 'companies.company_name')
            ->get();

        return response()->json($company_summary);
    }

    public function expense_index()
    {
        $payment_methods = DB::table('payment_methods')->get();
        $companies       = DB::table('companies')->get();

        return view('company_reports.expense_index', compact('payment_methods', 'companies'));
    }

    public function inventory_index()
    {
        $companies = DB::table('companies')->get();

        return view('company_reports.inventory_index', compact('companies'));
    }

    public function record(Request $request)
    {
        $companyId = $request->company_id ?: auth()->user()->company_id;
        $data_array = $this->bookingReportService->getReportData($companyId, $request->all());

        if (empty($data_array)) {
            return back()->with('error', 'Sorry no record found.');
        }

        return view('reports.reports_by_types')->with('report_data', $data_array);
    }

    public function expense_record(Request $request)
    {
        $this->validate($request, [
            'company_id' => 'required',
        ], [
            'company_id.required' => 'Please select Expense type',
        ]);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $type      = $request->company_id;

        $expenses = Expense::select('expenses.*', 'expense_types.type', 'payment_methods.method', 'companies.company_name')
            ->join('expense_types', 'expense_types.id', '=', 'expenses.expense_type')
            ->join('companies', 'companies.id', '=', 'expenses.company_id')
            ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method');

        if (!empty($startDate) && !empty($endDate)) {
            $expenses->whereBetween('expenses.payable', [$startDate, $endDate]);
        }

        if ($request->company_id != 'no') {
            $expenses->where('expenses.company_id', $request->company_id);
        }

        if ($request->filled('payment_method') && $request->payment_method != 'no') {
            $expenses->where('expenses.payment_method', $request->payment_method);
        }

        $expense_report = $expenses->orderBy('expenses.created_at', 'desc')->get();

        $view = view('company_reports.expense_reports', [
            'expense_report' => $expense_report,
            'start_date'     => $startDate ?: 'no_start_date',
            'end_date'       => $endDate ?: 'no_end_date',
            'type'           => $type,
            'method'         => $request->payment_method
        ])->render();

        return response()->json(['view' => $view]);
    }

    public function inventory_record(Request $request)
    {
        $this->validate($request, [
            'company_id' => 'required',
        ], [
            'company_id.required' => 'Please select Expense type',
        ]);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $companyId = $request->company_id;

        $query = InventoryRemain::with('inventory')
            ->where('company_id', $companyId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $inventory_report = $query->orderBy('created_at', 'desc')->get();

        $view = view('company_reports.inventory_reports', [
            'inventory_report' => $inventory_report,
            'start_date'       => $startDate ?: 'no_start_date',
            'end_date'         => $endDate ?: 'no_end_date',
            'type'             => $companyId,
            'method'           => $request->payment_method
        ])->render();

        return response()->json(['view' => $view]);
    }
}

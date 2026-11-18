<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Reports\BookingReportService;
use App\Services\Reports\InventoryReportService;
use App\Services\Reports\ExpenseReportService;
use App\Services\Reports\ProfitLossReportService;

class ReportController extends Controller
{
    public function __construct(
        protected BookingReportService $bookingReportService,
        protected InventoryReportService $inventoryReportService,
        protected ExpenseReportService $expenseReportService,
        protected ProfitLossReportService $profitLossReportService
    ) {}

    public function index()
    {
        if (! (auth()->user() && auth()->user()->can('view bookings report'))) {
            abort(403);
        }

        $companyId = auth()->user()->company_id;

        $categories = DB::table('categories')
            ->where('is_active', 1)
            ->where('company_id', $companyId)
            ->get();

        $services = DB::table('services')
            ->where('is_active', 1)
            ->where('company_id', $companyId)
            ->get();

        $users = DB::table('users')
            ->where('is_active', 1)
            ->where('company_id', $companyId)
            ->get();

        return view('reports.index', compact('categories', 'services', 'users'));
    }

    public function graph_report()
    {
        if (! (auth()->user() && auth()->user()->can('view services report'))) {
            abort(403);
        }

        $start_date = date('Y-m-01');
        $end_date   = date('Y-m-d');

        return view('reports.graph_report', compact('start_date', 'end_date'));
    }

    public function heat_graph_report()
    {
        $services = DB::table('services')
            ->where('company_id', auth()->user()->company_id)
            ->where('is_active', 1)
            ->get();

        return view('reports.heatmap', compact('services'));
    }

    public function customer_graph_report(Request $request)
    {
        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-t');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate   = date('Y-m-d', strtotime($request->end_date));
        }

        $referredcustomers = DB::table('service_bookings')
            ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
            ->whereDate('service_bookings.due_date', '>=', $startDate)
            ->whereDate('service_bookings.due_date', '<=', $endDate)
            ->whereNotIn('service_bookings.service_status', [4, 5])
            ->select('customers.referred_by', DB::raw('count(*) as total'))
            ->where('service_bookings.company_id', auth()->user()->company_id)
            ->groupBy('customers.referred_by')
            ->get();

        return response()->json($referredcustomers);
    }

    public function customer_graph()
    {
        if (! (auth()->user() && auth()->user()->can('view customer graph'))) {
            abort(403);
        }

        return view('reports.customer_graph');
    }

    public function customer_country_graph_report(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('view customer country graph'))) {
            abort(403);
        }

        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-t');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate   = date('Y-m-d', strtotime($request->end_date));
        }

        $customers_by_country = DB::table('service_bookings')
            ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
            ->join('countries', DB::raw("TRIM(LEADING '+' FROM countries.phone_code)"), '=', 'customers.country_code')
            ->whereDate('service_bookings.due_date', '>=', $startDate)
            ->whereDate('service_bookings.due_date', '<=', $endDate)
            ->select('countries.name as country', DB::raw('COUNT(DISTINCT customers.id) as total'))
            ->where('service_bookings.company_id', auth()->user()->company_id)
            ->groupBy('customers.country_code')
            ->get();

        return response()->json([
            'countries' => $customers_by_country
        ]);
    }

    public function customer_country_graph()
    {
        return view('reports.customer_country_graph');
    }

    public function profit_loss_report()
    {
        if (! (auth()->user() && auth()->user()->can('view profit & loss graph'))) {
            abort(403);
        }

        $start_date = date('Y-m-01');
        $end_date   = date('Y-m-d');

        return view('reports.profit_loss_report', compact('start_date', 'end_date'));
    }

    public function zipcode_report()
    {
        return view('reports.postcode_graph');
    }

    public function zipcode_data(Request $request)
    {
        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-t');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate   = date('Y-m-d', strtotime($request->end_date));
        }

        $referredcustomers = DB::table('service_bookings')
            ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
            ->whereDate('service_bookings.due_date', '>=', $startDate)
            ->whereDate('service_bookings.due_date', '<=', $endDate)
            ->whereNotIn('service_bookings.service_status', [4, 5])
            ->select(
                DB::raw('(CASE WHEN LENGTH(zip)>5 THEN SUBSTRING(zip, 1, 3) ELSE SUBSTRING(zip, 1, 2) END) as zip'),
                DB::raw('count(*) as total')
            )
            ->where('service_bookings.company_id', auth()->user()->company_id)
            ->groupBy(DB::raw('(CASE WHEN LENGTH(zip)>5 THEN SUBSTRING(zip, 1, 3) ELSE SUBSTRING(zip, 1, 2) END)'))
            ->orderBy('customers.zip', 'DESC')
            ->get();

        return response()->json($referredcustomers);
    }

    public function profit_loss_data(Request $request)
    {
        $data = $this->profitLossReportService->getProfitLossData(
            auth()->user()->company_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json($data);
    }

    public function get_booking_chart(Request $request)
    {
        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-d');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate   = date('Y-m-d', strtotime($request->end_date));
        }

        $companyId = auth()->user()->company_id;

        $service_info = DB::table('service_bookings')
            ->join('booking_details', 'service_bookings.id', '=', 'booking_details.booking_id')
            ->join('services', 'booking_details.service_id', '=', 'services.id')
            ->where('service_bookings.company_id', $companyId)
            ->whereBetween('service_bookings.due_date', [$startDate, $endDate])
            ->where('service_bookings.service_status', 3)
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('ROUND(COALESCE(SUM(services.price), 0), 2) as total_amount'),
                'services.name',
                'services.service_color'
            )
            ->groupBy('booking_details.service_id', 'services.name', 'services.service_color')
            ->get();

        return response()->json($service_info);
    }

    public function show_heat_report(Request $request)
    {
        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-d');

        if ($request->filled('graphs_dates')) {
            $dates = explode('-', $request->graphs_dates);
            if (count($dates) >= 2) {
                $startDate = date('Y-m-d', strtotime(trim($dates[0])));
                $endDate   = date('Y-m-d', strtotime(trim($dates[1])));
            }
        }

        $query = DB::table('service_bookings')
            ->join('booking_details', 'service_bookings.id', '=', 'booking_details.booking_id')
            ->join('services', 'booking_details.service_id', '=', 'services.id')
            ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
            ->where('service_bookings.company_id', auth()->user()->company_id)
            ->where('service_bookings.service_status', '!=', 5)
            ->whereDate('service_bookings.due_date', '>=', $startDate)
            ->whereDate('service_bookings.due_date', '<=', $endDate);

        if ($request->filled('service')) {
            $query->where('booking_details.service_id', $request->service);
        }

        $info = $query->select(
            'services.name',
            'services.service_color',
            'customers.zip',
            'customers.first_name',
            'customers.last_name'
        )->get();

        return response()->json($info);
    }

    public function customer_index()
    {
        if (! (auth()->user() && auth()->user()->can('view customer report'))) {
            abort(403);
        }

        $customers = DB::table('customers')
            ->where('company_id', auth()->user()->company_id)
            ->get();

        return view('reports.customer_index', compact('customers'));
    }

    public function expense_index()
    {
        if (! (auth()->user() && auth()->user()->can('view expense report'))) {
            abort(403);
        }

        $expense_types = DB::table('expense_types')
            ->where('company_id', auth()->user()->company_id)
            ->get();

        $payment_methods = DB::table('payment_methods')->get();

        return view('reports.expense_index', compact('expense_types', 'payment_methods'));
    }

    public function record(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $data_array = $this->bookingReportService->getReportData($companyId, $request->all());

        if (empty($data_array)) {
            return back()->with('error', 'Sorry no record found.');
        }

        return view('reports.reports_by_types')->with('report_data', $data_array);
    }

    public function customer_record(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            // 'start_date'   => 'required|date',
            // 'end_date'     => 'required|date|after_or_equal:start_date',
        ], [
            'customer_id.required' => 'Please select customer',
        ]);

        $companyId   = auth()->user()->company_id;
        $customerId  = $request->customer_id;
        $startDate   = $request->start_date ? ($request->start_date . ' 00:00:00') : 'no-date';
        $endDate     = $request->end_date ? ($request->end_date . ' 23:59:59') : 'no-date';

        $report = $this->bookingReportService->getCustomerReportData($companyId, [
            'customer_id' => $customerId,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
        ]);

        $data_array   = $report['data_array'];
        $total_amount = $report['total_amount'];

        if (empty($data_array)) {
            return back()->with('error', 'Sorry no record found.');
        }

        return view('reports.customer_reports', compact(
            'data_array',
            'total_amount',
            'startDate',
            'endDate',
            'customerId'
        ))->with([
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'customer_id' => $customerId,
        ]);
    }

    public function inventory_index()
    {
        $inventories = DB::table('inventories')
            ->where('company_id', auth()->user()->company_id)
            ->select('id', 'name')
            ->get();

        return view('reports.inventory_index', compact('inventories'));
    }

    public function inventory_record(Request $request)
    {
        $request->validate([
            // 'start_date'   => 'required|date',
            // 'end_date'     => 'required|date|after_or_equal:start_date',
            'inventory_id' => 'nullable|integer|exists:inventories,id',
        ], [
            // 'start_date.required'     => 'Please select a start date',
            // 'end_date.required'       => 'Please select an end date',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
        ]);

        $companyId   = auth()->user()->company_id;
        $startDate   = $request->start_date;
        $endDate     = $request->end_date;
        $inventoryId = $request->inventory_id;

        $report = $this->inventoryReportService->getReportData($companyId, [
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'inventory_id' => $inventoryId,
        ]);

        $data_array          = $report['records'];
        $total_quantity_used = $report['total_quantity_used'];

        if (empty($data_array)) {
            return back()->with('error', 'Sorry, no inventory usage records found for the selected criteria.');
        }

        return view('reports.inventory_reports', compact(
            'data_array',
            'total_quantity_used'
        ))->with([
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'inventory_id' => $inventoryId,
        ]);
    }

    public function reportInventoryCsv($start_date, $end_date, $inventory_id)
    {
        return $this->inventoryReportService->streamCsvExport(
            auth()->user()->company_id,
            $start_date,
            $end_date,
            $inventory_id
        );
    }

    public function expense_record(Request $request)
    {
        $this->validate($request, [
            'expense_type' => 'required',
        ], [
            'expense_type.required' => 'Please select Expense type',
        ]);

        $companyId = auth()->user()->company_id;
        $startDate = $request->start_date ?? 'no_start_date';
        $endDate   = $request->end_date ?? 'no_end_date';
        $type      = $request->expense_type;

        $expense_report = $this->expenseReportService->getReportData($companyId, [
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'expense_type'   => $request->expense_type,
            'payment_method' => $request->payment_method,
        ]);

        if ($expense_report->isEmpty()) {
            return back()->with('error', 'Sorry, no expense records found for the selected criteria.');
        }

        return view('reports.expense_reports', [
            'expense_report' => $expense_report,
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'type'           => $type,
            'method'         => $request->payment_method ?? 'no',
        ]);
    }

    public function reportCsv(Request $request, $start_date, $end_date, $category_id, $service_id, $status_id, $payment_type)
    {
        return $this->bookingReportService->streamCsvExport(auth()->user()->company_id, [
            'start_date'         => $start_date,
            'end_date'           => $end_date,
            'category_id'        => $category_id,
            'service_id'         => $service_id,
            'status_id'          => $status_id,
            'payment_instrument' => $payment_type,
            'user_id'            => $request->query('user_id'),
        ]);
    }

    public function reportCustomerCsv(Request $request, $start_date, $end_date, $customer_id)
    {
        return $this->bookingReportService->streamCustomerCsvExport(
            auth()->user()->company_id,
            $start_date,
            $end_date,
            $customer_id
        );
    }

    public function reportExpenseCsv(Request $request, $start_date, $end_date, $type, $method)
    {
        return $this->expenseReportService->streamCsvExport(auth()->user()->company_id, [
            'start_date'     => $start_date,
            'end_date'       => $end_date,
            'expense_type'   => $type,
            'payment_method' => $method,
        ]);
    }

    public function limits()
    {
        $company = auth()->user()->company;

        if (!$company) {
            abort(404, 'Company not found.');
        }

        $limits = $company->currentLimits();

        return view('reports.plan_limits', compact('company', 'limits'));
    }
}

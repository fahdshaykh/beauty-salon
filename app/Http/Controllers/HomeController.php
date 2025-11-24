<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use Session;

use App\RolePermissions;

use App\ServiceBooking;

use App\Customer;

use Illuminate\Support\Facades\DB;
use App\Expense;
use App\Models\Supplier;
use Carbon\Carbon;

class HomeController extends Controller

{

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('auth');

    }

    public function index()
    {
        $user = auth()->user();
        $companyId = $user->company_id ?? null;

        if (auth()->user()->hasRole('admin')) {

            $currentMonth = now()->month;
            $currentYear  = now()->year;

            $monthName = now()->format('F');

            // Customers this month
            $totalCustomers = Customer::where('company_id', $companyId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            // Suppliers this month
            $totalSuppliers = Supplier::where('company_id', $companyId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            // Expenses this month
            $totalExpense = Expense::where('company_id', $companyId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('amount');

            // Bookings this month
            $bookingsQuery = ServiceBooking::where('company_id', $companyId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear);

            $totalBookings = $bookingsQuery->count();
            $totalSales    = $bookingsQuery->sum(DB::raw('CAST(total_price AS DECIMAL(10,2))'));

            $totalProfit = $totalSales - $totalExpense;

            return view('home', compact(
                'totalCustomers',
                'totalBookings',
                'totalSuppliers',
                'totalExpense',
                'totalSales',
                'totalProfit',
                'monthName'
            ));

        } else {
            $bookingsAssign = ServiceBooking::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->where('service_status', '1')
                ->orWhere('service_status', '2')
                ->count();

            $bookingsCompleted = ServiceBooking::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->where('service_status', '3')
                ->count();

            return view('home', compact(
                'bookingsAssign',
                'bookingsCompleted'
            ));
        }

        
    }

    public function bookingOverviewData(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Get monthly booking count for current year
        $monthlyData = ServiceBooking::where('company_id', $companyId)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = $monthlyData->get($i, 0);
        }

        return response()->json([
            'labels' => $months,
            'data'   => $data
        ]);
    }

    public function salesStatusData(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $today = Carbon::today();

        $todayBooked = ServiceBooking::where('company_id', $companyId)
            ->whereDate('created_at', $today)
            ->count();

        $todayInProgress = ServiceBooking::where('company_id', $companyId)
            ->whereDate('created_at', $today)
            ->where('service_status', '2')
            ->count();

        $todayCompleted = ServiceBooking::where('company_id', $companyId)
            ->whereDate('created_at', $today)
            ->where('service_status', '3')
            ->count();

        $todayCancelled = ServiceBooking::where('company_id', $companyId)
            ->whereDate('created_at', $today)
            ->where('service_status', '4')
            ->count();

        return response()->json([
            'data' => [$todayBooked, $todayInProgress, $todayCompleted, $todayCancelled],
            'labels' => ['Today Booked', 'Today In Progress', 'Today Completed', 'Today Cancelled']
        ]);
    }

    public function dailySalesData(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $today = Carbon::today();

        $todayExpense = Expense::where('company_id', $companyId)
            ->whereDate('payable', $today)
            ->sum('amount');

        $todaySales = ServiceBooking::where('company_id', $companyId)
            ->whereDate('created_at', $today)
            ->where('service_status', '3')
            ->sum(DB::raw('CAST(total_price AS DECIMAL(10,2))'));

        $todayProfit = $todaySales - $todayExpense;

        return response()->json([
            'data' => [$todayExpense, $todaySales, $todayProfit > 0 ? $todayProfit : 0],
            'labels' => ['Today Expense', 'Today Sales', 'Today Profit'],
            'profit' => $todayProfit
        ]);
    }

}
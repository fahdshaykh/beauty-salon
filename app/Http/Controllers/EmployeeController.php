<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function employee_graph_report()
    {
        if (! (auth()->user() && auth()->user()->can('view employee report'))) {
            abort(403);
        }

        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');

        return view('employee_reports.graph_report', compact('start_date', 'end_date'));
    }

    public function employee_booking_chart(Request $request)
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
        }

        $companyId = auth()->user()->company_id;

        $employee_summary = DB::table('service_bookings')
            ->join('users', 'service_bookings.user_id', '=', 'users.id')
            ->where('service_bookings.company_id', $companyId)
            ->whereBetween('service_bookings.due_date', [$start_date, $end_date])
            ->where('service_bookings.service_status', 3)
            ->select(
                DB::raw('COUNT(service_bookings.id) as total_services'),
                DB::raw('ROUND(COALESCE(SUM(service_bookings.total_price), 0), 2) as total_revenue'),
                'users.name as employee_name'
            )
            ->groupBy('service_bookings.user_id', 'users.name')
            ->orderByDesc('total_revenue')
            ->get();

        return response()->json($employee_summary);
    }
}
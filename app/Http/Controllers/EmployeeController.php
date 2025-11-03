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

        return view('employee_reports.graph_report');

    }

    public function employee_booking_chart(Request $request)
    {
        if ($request->start_date && $request->end_date) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
        } else {
            $start_date = now()->startOfMonth();
            $end_date = now();
        }

        // $start_date = now()->startOfMonth()->toDateString();
        // $end_date = now()->toDateString();
        // dd($start_date, $end_date);
        
        $employee_summary = DB::table('service_bookings')
            ->join('users', 'service_bookings.user_id', '=', 'users.id')
            ->whereBetween('service_bookings.due_date', [$start_date, $end_date])
            ->whereNotIn('service_bookings.service_status', [4, 5]) // Exclude canceled/completed/etc.
            ->where('service_bookings.company_id', auth()->user()->company_id)
            ->select(
                DB::raw('COUNT(service_bookings.id) as total_services'),
                DB::raw('SUM(service_bookings.total_price) as total_revenue'),
                'users.name as employee_name'
            )
            ->groupBy('service_bookings.user_id', 'users.name')
            ->orderByDesc(DB::raw('SUM(service_bookings.total_price)'))
            ->get();
        
        return response()->json($employee_summary);
        
    }
}
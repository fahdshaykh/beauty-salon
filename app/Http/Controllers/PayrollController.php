<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class PayrollController extends Controller
{
    // public function index()
    // {
    //     $startOfPrevMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();
    //     $endOfPrevMonth = Carbon::now()->subMonth()->endOfMonth()->toDateString();
    
    //     $users = User::where('company_id', auth()->user()->company_id)
    //         ->whereHas('payrolls', function ($query) use ($startOfPrevMonth, $endOfPrevMonth) {
    //             $query->where('pay_period_start', '>=', $startOfPrevMonth)
    //                   ->where('pay_period_end', '<=', $endOfPrevMonth);
    //         })
    //         ->get();
    
    //     return view('payrolls.index', compact('users'));
    // }

    public function index(Request $request)
    {
        $month = $request->input('month') ?? Carbon::now()->subMonth()->month;
        $year = $request->input('year') ?? Carbon::now()->subMonth()->year;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $users = User::where('company_id', auth()->user()->company_id)
            ->whereHas('payrolls', function ($query) use ($startDate, $endDate) {
                $query->where('pay_period_start', '>=', $startDate)
                    ->where('pay_period_end', '<=', $endDate);
            })
            ->with(['payrolls' => function ($query) use ($startDate, $endDate) {
                $query->where('pay_period_start', '>=', $startDate)
                    ->where('pay_period_end', '<=', $endDate);
            }])
            ->get();

        return view('payrolls.index', compact('users', 'month', 'year'));
    }

    public function payslip(Request $request, $id)
    {
        $month = $request->input('month', now()->month); // default to current month
        $year = $request->input('year', now()->year); // default to current year

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $daysInMonth = $end->diffInDays($start) + 1;

        $user = User::where('id', $id)->where('is_deleted', 0)->first();

        if (!$user || is_null($user->base_salary)) {
            return redirect()->back()->with('error', 'This user has no base salary set. Cannot generate payslip.');
        }

        $monthlyWorkingHours = 6 * 8 * 4;
        $baseSalary = $user->base_salary;

        $commission = $user->serviceBookings()
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_price') * ($user->commission_rate / 100);

        $attendances = $user->attendances()->whereBetween('created_at', [$start, $end]);

        $hourlyRate = $monthlyWorkingHours > 0 ? $baseSalary / $monthlyWorkingHours : 0;

        $overtimeMinutes = $attendances->sum('overtime_minutes');
        $overtimeHours = $overtimeMinutes / 60;
        $overtimePayment = $overtimeHours * $hourlyRate;

        $unpaidLeaves = $attendances->where('status', 'A')->count();
        $leaveDeduction = 0;

        if ($unpaidLeaves > 2) {
            $extraLeaves = $unpaidLeaves - 2;
            $leaveHours = $extraLeaves * 8;
            $leaveDeduction = $leaveHours * $hourlyRate;
        }

        $transportDeduction = ($user->is_transport === 'yes' && $user->transport_allowance)
            ? $user->transport_allowance
            : 0;

        $taxableIncome = $baseSalary + $commission + $overtimePayment;
        $tax = 0.10 * $taxableIncome;

        $totalDeductions = $leaveDeduction + $transportDeduction + $tax;
        $netPay = $taxableIncome - $totalDeductions;

        $pdf = PDF::loadView('paystub', [
            'user' => $user,
            'base_salary' => round($baseSalary, 2),
            'commission' => round($commission, 2),
            'overtime_payment' => round($overtimePayment, 2),
            'deductions' => round($totalDeductions, 2),
            'transport_deduction' => round($transportDeduction, 2),
            'net_pay' => round($netPay, 2),
            'pay_period_start' => $start,
            'pay_period_end' => $end,
        ]);

        $filename = 'paystub_' . $user->id . '_' . $start->format('Ym') . '.pdf';

        return $pdf->download($filename);
    }



    

}

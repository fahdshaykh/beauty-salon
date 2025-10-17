<?php

namespace App\Console\Commands;

use App\Models\Payroll;
use App\User;
use Illuminate\Console\Command;

class CalculatePayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:payroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start = now()->subMonth()->startOfMonth();
        $end = now()->subMonth()->endOfMonth();

        // Monthly hours: 6 working days/week * 8 hours/day * 4.33 weeks ≈ 208 hours
        $monthlyWorkingHours = 6 * 8 * 4;

        $users = User::where('is_deleted', 0)->whereNotNull('base_salary')->get();

        foreach ($users as $user) {

            $alreadyExists = Payroll::where('user_id', $user->id)
            ->whereDate('pay_period_start', $start)
            ->whereDate('pay_period_end', $end)
            ->exists();

            if ($alreadyExists) {
                $this->info("Payroll already exists for user ID {$user->id} for {$start->format('F Y')}");
                continue;
            }


            $baseSalary = $user->base_salary;

            // 1. Commission from services
            $commission = $user->serviceBookings()
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_price') * ($user->commission_rate / 100);

            // 2. Attendances in pay period
            $attendances = $user->attendances();

            // 3. Hourly Rate
            $hourlyRate = $monthlyWorkingHours > 0 ? $baseSalary / $monthlyWorkingHours : 0;

            // 4. Overtime Calculation
            $overtimeMinutes = $attendances->sum('overtime_minutes');
            $overtimeHours = $overtimeMinutes / 60;
            $overtimePayment = $overtimeHours * $hourlyRate;

            // 5. Leave Deduction (status = 'A')
            $unpaidLeaves = $attendances->where('status', 'A')->count();
            $leaveDeduction = 0;

            if ($unpaidLeaves > 2) {
                $extraLeaves = $unpaidLeaves - 2;
                $leaveHours = $extraLeaves * 8;
                $leaveDeduction = $leaveHours * $hourlyRate;
            }

            // 6. Transport Deduction
            $transportDeduction = ($user->is_transport === 'yes' && $user->transport_allowance)
                ? $user->transport_allowance
                : 0;

            // 7. Tax Deduction (10%)
            $taxableIncome = $baseSalary + $commission + $overtimePayment;
            $tax = 0.10 * $taxableIncome;

            // 8. Net Pay
            $totalDeductions = $leaveDeduction + $transportDeduction + $tax;
            $netPay = $taxableIncome - $totalDeductions;

            // 9. Save to Payroll
            Payroll::create([
                'user_id'          => $user->id,
                'base_salary'      => round($baseSalary, 2),
                'commission'       => round($commission, 2),
                'overtime_payment' => round($overtimePayment, 2),
                'deductions'       => round($totalDeductions, 2),
                'net_pay'          => round($netPay, 2),
                'pay_period_start' => $start,
                'pay_period_end'   => $end,
            ]);
        }

        $this->info('Payroll generated successfully with accurate calculations.');
    }











     /*

     for managing late penalties

    public function handle()
    {
        $start = now()->subMonth()->startOfMonth();
        $end = now()->subMonth()->endOfMonth();
        $workingDaysInMonth = 26;

        $users = User::where('is_deleted', 0)->whereNotNull('base_salary')->get();

        foreach ($users as $user) {
            $baseSalary = $user->base_salary;

            // 1. Commission
            $commission = $user->serviceBookings()
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_price') * ($user->commission_rate / 100);

            // 2. Overtime
            $overtimeMinutes = $user->attendances()
                ->whereBetween('created_at', [$start, $end])
                ->sum('overtime_minutes');

            $hourlyRate = $baseSalary / 160;
            $overtimePayment = ($overtimeMinutes / 60) * $hourlyRate;

            // 3. Late Penalty (e.g., 100 per late)
            $lateCount = $user->attendances()
                ->whereBetween('created_at', [$start, $end])
                ->where('is_late', 1)
                ->count();
            $latePenalty = $lateCount * 100;

            // 4. Absences (No attendance entries for a working day)
            $attendanceDates = $user->attendances()
                ->whereBetween('created_at', [$start, $end])
                ->pluck('created_at')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->unique();

            $presentDays = $attendanceDates->count();
            $absentDays = max(0, $workingDaysInMonth - $presentDays);
            $dailyRate = $baseSalary / $workingDaysInMonth;
            $absentDeduction = $absentDays * $dailyRate;

            // 5. Tax (10%)
            $tax = 0.10 * ($baseSalary + $commission);

            // 6. Total Deductions
            $totalDeductions = $tax + $latePenalty + $absentDeduction;

            // 7. Net Pay
            $netPay = $baseSalary + $commission + $overtimePayment - $totalDeductions;

            Payroll::create([
                'user_id' => $user->id,
                'base_salary' => $baseSalary,
                'commission' => $commission,
                'overtime_payment' => $overtimePayment,
                'deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'pay_period_start' => $start,
                'pay_period_end' => $end,
            ]);
        }

        $this->info('Payroll with penalties and deductions generated successfully.');
    }



    */


}

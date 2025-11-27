<?php

namespace App\Console\Commands;

use App\Models\Payroll;
use App\User;
use Illuminate\Console\Command;
use App\Models\WorkSchedule;
use Carbon\Carbon;

class CalculatePayroll extends Command
{
    protected $signature = 'calculate:payroll {--month= : YYYY-MM (default: previous month)}';
    protected $description = 'Generate payroll for all employees based on work schedules';

    public function handle()
    {
        // -------------------------------------------------
        // 1. Determine pay period
        // -------------------------------------------------
        $input = $this->option('month');
        $period = $input ? Carbon::createFromFormat('Y-m', $input) : now()->subMonth();

        $start = $period->copy()->startOfMonth();
        $end   = $period->copy()->endOfMonth();

        $this->info("Generating payroll for {$start->format('F Y')}");

        // -------------------------------------------------
        // 2. Load all active users with salary
        // -------------------------------------------------
        $users = User::where('is_deleted', 0)
                     ->whereNotNull('base_salary')
                     ->where('base_salary', '>', 0)
                     ->get();

        foreach ($users as $user) {
            // Skip if payroll already exists for this period
            $exists = Payroll::where('user_id', $user->id)
                             ->whereDate('pay_period_start', $start)
                             ->whereDate('pay_period_end', $end)
                             ->exists();

            if ($exists) {
                $this->line("  → Payroll already exists for {$user->name}");
                continue;
            }

            // -------------------------------------------------
            // 3. Calculate **scheduled working hours** for this employee
            // -------------------------------------------------
            $scheduledHours = $this->getScheduledHours($user->company_id, $start, $end);

            if ($scheduledHours <= 0) {
                $this->warn("  → No schedule for {$user->name} – skipping");
                continue;
            }

            $baseSalary = (float) $user->base_salary;
            $hourlyRate = $baseSalary / $scheduledHours;

            // -------------------------------------------------
            // 4. Commission
            // -------------------------------------------------
            $commission = $user->serviceBookings()
                               ->whereBetween('created_at', [$start, $end])
                               ->sum('total_price') * ($user->commission_rate / 100);

            // -------------------------------------------------
            // 5. Attendances – use check_in date instead of non-existent 'date' column
            // -------------------------------------------------
            $attendances = $user->attendances()
                ->whereNotNull('check_in')
                ->whereBetween('check_in', [$start->startOfDay(), $end->endOfDay()]);

            // If no attendance records in the month
            if ($attendances->count() === 0) {
                $this->warn("  → No attendance records for {$user->name} in this period");
            }

            // Overtime
            $overtimeMinutes = $attendances->sum('overtime_minutes');
            $overtimePay     = ($overtimeMinutes / 60) * $hourlyRate;

            // Late penalty
            $lateCount   = $attendances->where('is_late', 1)->count();
            $latePenalty = $lateCount * 100;

            // Unpaid leaves / Absent days
            $absentDays = $attendances->where('status', 'A')->count(); // assuming 'A' = Absent
            $leaveDeduction = 0;
            if ($absentDays > 2) {
                $extraDays = $absentDays - 2;
                $leaveDeduction = $extraDays * 8 * $hourlyRate; // 8 hours per day deduction
            }

            // -------------------------------------------------
            // 6. Transport Allowance (added to income)
            // -------------------------------------------------
            $transport = ($user->is_transport === 'yes') ? (float) $user->transport_allowance : 0;

            // -------------------------------------------------
            // 7. Taxable income & Tax (10%)
            // -------------------------------------------------
            $taxable = $baseSalary + $commission + $overtimePay + $transport;
            $tax     = 0.10 * $taxable;

            // -------------------------------------------------
            // 8. Total Deductions & Net Pay
            // -------------------------------------------------
            $totalDeductions = $leaveDeduction + $latePenalty + $tax;
            $netPay = $taxable - $totalDeductions;

            // -------------------------------------------------
            // 9. Save Payroll
            // -------------------------------------------------
            Payroll::create([
                'user_id'           => $user->id,
                'company_id'        => $user->company_id,
                'base_salary'       => round($baseSalary, 2),
                'commission'        => round($commission, 2),
                'overtime_payment'  => round($overtimePay, 2),
                'transport'         => round($transport, 2),
                'late_penalty'      => round($latePenalty, 2),
                'leave_deduction'   => round($leaveDeduction, 2),
                'tax'               => round($tax, 2),
                'deductions'        => round($totalDeductions, 2),
                'net_pay'           => round($netPay, 2),
                'pay_period_start'  => $start,
                'pay_period_end'    => $end,
                'scheduled_hours'   => $scheduledHours,
            ]);

            $this->info("  → Payroll created for {$user->name}: PKR " . number_format($netPay, 2));
        }

        $this->info('Payroll generation completed.');
    }

    /**
     * Calculate total scheduled working hours for a company in a date range.
     */
    // private function getScheduledHours(int $companyId, Carbon $start, Carbon $end): float
    // {
    //     $schedules = WorkSchedule::where('company_id', $companyId)
    //                              ->where('is_active', 1)
    //                              ->get();

    //     if ($schedules->isEmpty()) {
    //         return 0;
    //     }

    //     $totalMinutes = 0;

    //     $current = $start->copy();
    //     while ($current->lte($end)) {
    //         $dayName = $current->englishDayOfWeek; // Monday, Tuesday...

    //         $schedule = $schedules->firstWhere('day', $dayName);
    //         if ($schedule) {
    //             $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
    //             $endTime   = Carbon::createFromFormat('H:i:s', $schedule->end_time);
    //             $minutes   = $endTime->diffInMinutes($startTime);
    //             $totalMinutes += $minutes;
    //         }

    //         $current->addDay();
    //     }

    //     return $totalMinutes / 60; // return hours
    // }

    private function getScheduledHours(int $companyId, Carbon $start, Carbon $end): float
    {
        // Get only active schedules for this company
        $schedules = WorkSchedule::where('company_id', $companyId)
                                ->where('is_active', 1)
                                ->get()
                                ->keyBy('day'); // Key by day name: ['Monday' => model, ...]

        if ($schedules->isEmpty()) return 0;

        $totalMinutes = 0;
        $date = $start->copy();

        while ($date->lte($end)) {
            $dayName = $date->format('l'); // Returns: Monday, Tuesday, etc. → matches your DB

            if ($schedules->has($dayName)) {
                $schedule = $schedules[$dayName];

                // Use Carbon::parse() → super safe, handles 09:00:00 perfectly
                $from = Carbon::parse($schedule->start_time);
                $to   = Carbon::parse($schedule->end_time);

                if ($to->lte($from)) {
                    $to->addDay(); // overnight shift
                }

                $totalMinutes += $to->diffInMinutes($from);
            }

            $date->addDay();
        }

        return round($totalMinutes / 60, 2);
    }
}

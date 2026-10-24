<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Models\EmployeeAttendance;
use App\Models\WorkSchedule;
use Carbon\Carbon;

class AutoUpdateCheckout extends Command
{
    protected $signature = 'attendance:auto-checkout';
    protected $description = 'Auto update check-out time for employees who forgot to check out';

    public function handle()
    {
        $today = now()->toDateString();
        $dayName = now()->format('l'); // e.g., Monday

        $schedule = WorkSchedule::where('day', $dayName)->first();

        if (!$schedule) {
            $this->error("Work schedule for $dayName not found.");
            return;
        }

        $defaultCheckout = Carbon::parse($today . ' ' . $schedule->end_time);

        $attendances = EmployeeAttendance::whereDate('created_at', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->get();

        foreach ($attendances as $attendance) {
            $checkIn = Carbon::parse($attendance->check_in);
            $hoursWorked = $checkIn->diffInMinutes($defaultCheckout);
            $overtime = max(0, $defaultCheckout->diffInMinutes(Carbon::parse($schedule->end_time)));

            $attendance->update([
                'check_out' => $defaultCheckout,
                'check_out_location' => 'SYSTEM_CRON',
                'hours_worked' => $hoursWorked,
                'overtime_minutes' => $overtime,
            ]);

            $this->info("Auto check-out applied for User ID: {$attendance->user_id}");
        }

        $this->info('Auto check-out process completed.');
    }
}
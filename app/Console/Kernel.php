<?php

namespace App\Console;

use App\Console\Commands\SendNotificationEmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SmsCron::class,
        Commands\ScheduledEmail::class,
        Commands\SendEmails::class,
        Commands\SendNotificationEmail::class, // Added the SendNotificationEmail command
        \App\Console\Commands\SendCustomerBirthdayNotifications::class,
        \App\Console\Commands\CalculatePayroll::class,
        \App\Console\Commands\AutoCheckoutCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('sms:cron')
        //     ->monthlyOn(1, '06:00');

        // // Checking for scheduled emails and moving them to today's emails table
        // $schedule->command('scheduledemail:cron')
        //     ->hourly();
        
        // // Sending today's emails from today's email table
        // $schedule->command('sendemails:cron')
        //     ->everyMinute();

        $schedule->command('send:booking-reminders')->everyMinute();

        // Schedule the command to run every day at midnight
        $schedule->command('notify:customer-birthdays')->daily();

        $schedule->command('calculate:payroll')->monthlyOn(1, '01:00');

        $schedule->command('attendance:auto-checkout')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

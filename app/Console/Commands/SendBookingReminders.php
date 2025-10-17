<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendBookingReminderJob;
use App\ServiceBooking;
use Carbon\Carbon;

class SendBookingReminders extends Command
{
    protected $signature = 'send:booking-reminders';
    protected $description = 'Send email reminders for bookings 30 minutes before their start time.';

    public function handle()
    {
        // $now = Carbon::now();
        // $reminderTime = $now->addMinutes(5);

        // $bookings = ServiceBooking::where('due_date', $reminderTime->format('Y-m-d'))
        //     ->where('due_time', $reminderTime->format('H:i'))
        //     ->with('customer')
        //     ->get();

        // foreach ($bookings as $booking) {
        //     SendBookingReminderJob::dispatch($booking);
        //     $this->info('Reminder queued for: ' . $booking->customer->email);
        // }

        

        $now = Carbon::now();
        $reminderTime = $now->copy()->addMinutes(15);
        
        $currentTimeFormatted = $now->format('h:i A');
        $reminderTimeFormatted = $reminderTime->format('h:i A');


        $bookings = ServiceBooking::where('due_date', $reminderTime->format('Y-m-d'))
            ->whereRaw("STR_TO_DATE(due_time, '%h:%i %p') BETWEEN STR_TO_DATE(?, '%h:%i %p') AND STR_TO_DATE(?, '%h:%i %p')", [
                $currentTimeFormatted, 
                $reminderTimeFormatted, 
            ])
            ->with('customer')
            ->get();


        if ($bookings->isEmpty()) {
            $this->info('No bookings found for reminder.');
        } else {
            foreach ($bookings as $booking) {
                SendBookingReminderJob::dispatch($booking);
                $this->info('Reminder queued for: ' . $booking->customer->email);
            }
        }




    }
}

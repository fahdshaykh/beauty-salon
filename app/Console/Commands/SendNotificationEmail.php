<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationEmail;
use App\ServiceBooking;
use App\Jobs\SendBookingReminderJob;
use Carbon\Carbon;

class SendNotificationEmail extends Command
{
    protected $signature = 'email:send-notification';
    protected $description = 'Send a scheduled notification email';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        $reminderTime = $now->addMinutes(30);

        $bookings = ServiceBooking::where('date', $reminderTime->format('Y-m-d'))
            ->where('time', $reminderTime->format('H:i'))
            ->with('user')
            ->get();

        foreach ($bookings as $booking) {

            SendBookingReminderJob::dispatch($booking);
            
        }

        // $recipientEmail = 'asadwadani05@gmail.com'; // Update this to a valid email address

        // Mail::to($recipientEmail)->send(new NotificationEmail());
        
        // if (Mail::failures()) {
        //     $this->error('Failed to send email to ' . $recipientEmail);
        // } else {
        //     $this->info('Notification email sent successfully to ' . $recipientEmail);
        // }
    }

}

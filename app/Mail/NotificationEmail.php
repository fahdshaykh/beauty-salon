<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Scheduled Notification';
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content = null)
    {
        $this->content = $content ?? 'This is your scheduled notification email.';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.booking')
                    ->with([
                        'message' => $this->content,
                    ]);
    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class QuoteMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->data['subject'] ?? 'Quotation';
        $from = $this->data['from'] ?? null;
        $title = $this->data['title'] ?? null;

        $mail = $this->view('emails.quote')
            ->subject($subject)
            ->with(['quote' => $this->data['quote'], 'header' => $this->data['header'] ?? '', 'footer' => $this->data['footer'] ?? '', 'logo' => $this->data['logo'] ?? '']);

        if ($from) {
            $mail->from($from, $title);
        }

        return $mail;
    }
}

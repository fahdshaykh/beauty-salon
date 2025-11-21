<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class MarketingMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;
    public $viewName;
    public $subjectText;
    public $fromAddress;
    public $fromName;

    public function __construct(array $data, string $viewName = 'emails.marketing', string $subject = null, string $from = null)
    {
        $this->data = $data;
        $this->viewName = $viewName;
        $this->subjectText = $subject;
        $this->fromAddress = $from;
    }

    public function build()
    {
        $mail = $this->view($this->viewName)->with($this->data);

        if ($this->subjectText) {
            $mail->subject($this->subjectText);
        }

        if ($this->fromAddress) {
            $mail->from($this->fromAddress, $this->data['title'] ?? null);
        }

        return $mail;
    }
}

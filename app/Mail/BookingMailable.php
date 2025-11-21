<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\View;

class BookingMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $bookingData;
    public $customerEmail;
    public $subjectText;
    public $fromAddress;
    public $fromName;
    public $companyId;

    /**
     * Create a new message instance.
     *
     * @param array $bookingData   - Booking details array
     * @param string $customerEmail - Recipient email
     * @param string $subject       - Email subject
     * @param string $from          - From email address
     * @param string $title         - From name/title
     * @param int $companyId        - Company ID for template lookup
     * @return void
     */
    public function __construct(array $bookingData, string $customerEmail, string $subject = null, string $from = null, string $title = null, int $companyId = null)
    {
        $this->bookingData = $bookingData;
        $this->customerEmail = $customerEmail;
        $this->subjectText = $subject ?? 'Booking Confirmation';
        $this->fromAddress = $from;
        $this->fromName = $title;
        $this->companyId = $companyId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('emails.booking_confirmation')
            ->subject($this->subjectText)
            ->with([
                'bookingData' => $this->bookingData,
                'customer_name' => $this->bookingData['customer_name'] ?? '',
                'invoice_number' => $this->bookingData['invoice_number'] ?? '',
                'due_date' => $this->bookingData['due_date'] ?? '',
                'due_time' => $this->bookingData['due_time'] ?? '',
                'services' => $this->bookingData['services'] ?? '',
                'total_price' => $this->bookingData['total_price'] ?? '0.00',
                'vat' => $this->bookingData['vat'] ?? '0.00',
                'comment' => $this->bookingData['comment'] ?? '',
                'company_title' => $this->bookingData['company_title'] ?? 'Beauty Salon',
            ]);

        if ($this->fromAddress) {
            $mail->from($this->fromAddress, $this->fromName);
        }

        return $mail;
    }
}

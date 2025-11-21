<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class InvoiceMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $detailArray;
    public $toEmail;
    public $toName;
    public $subjectText;
    public $fromAddress;
    public $fromName;
    public $bookingId;

    /**
     * Create a new message instance.
     */
    public function __construct(array $detailArray, string $toEmail, string $toName, string $subject, string $fromAddress, string $fromName, int $bookingId)
    {
        $this->detailArray = $detailArray;
        $this->toEmail = $toEmail;
        $this->toName = $toName;
        $this->subjectText = $subject;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->bookingId = $bookingId;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Convert logo to base64 if it exists
        $detailArray = $this->detailArray;
        if (!empty($detailArray['company_logo'])) {
            $logoPath = public_path('email_logo/' . $detailArray['company_logo']);
            if (file_exists($logoPath)) {
                $logoData = file_get_contents($logoPath);
                
                // Detect MIME type from file extension
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mimeTypes = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    'webp' => 'image/webp'
                ];
                $mimeType = $mimeTypes[$ext] ?? 'image/jpeg';
                
                $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoData);
                $detailArray['company_logo_base64'] = $logoBase64;
            }
        }

        // Generate PDF on the fly when the job is processed
        $pdf = \PDF::loadView('emails.invoicepdf', ['detail_array' => $detailArray]);
        
        $mail = $this->view('emails.invoice')
            ->subject($this->subjectText)
            ->to($this->toEmail, $this->toName)
            ->from($this->fromAddress, $this->fromName)
            ->with(['detail_array' => $detailArray]);

        if ($pdf) {
            $mail->attachData($pdf->output(), "invoice.pdf");
        }

        return $mail;
    }
}

<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InvoiceIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['player','club','fee']);
        $this->subject('Invoice issued: '.$this->invoice->number.' - '.$this->invoice->club->name);
    }

    public function build()
    {
        $mail = $this->view('emails.invoice_issued')
            ->with(['invoice' => $this->invoice]);
        if ($this->invoice->pdf_path && Storage::disk('public')->exists($this->invoice->pdf_path)) {
            $fileName = 'Invoice-'.$this->invoice->number.'.pdf';
            $mail->attachFromStorageDisk('public', $this->invoice->pdf_path, $fileName, ['mime' => 'application/pdf']);
        }
        return $mail;
    }
}

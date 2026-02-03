<?php

namespace App\Mail;

use App\Models\MaintenanceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MaintenanceReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MaintenanceReport $report)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'WordPress Wartungsbericht - ' . $this->report->website->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.maintenance-report',
        );
    }

    public function attachments(): array
    {
        if ($this->report->pdf_path && Storage::disk('public')->exists($this->report->pdf_path)) {
            return [
                Attachment::fromStorage('public/' . $this->report->pdf_path)
                    ->as('Wartungsbericht.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}

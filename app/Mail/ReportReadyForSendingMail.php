<?php

namespace App\Mail;

use App\Models\AgencySettings;
use App\Models\MaintenanceReport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportReadyForSendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MaintenanceReport $report,
        public User $manager
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Wartungsbericht bereit zum Versand - {$this->report->website->name}",
        );
    }

    public function content(): Content
    {
        $agency = AgencySettings::instance();
        $entwicklerName = $this->report->entwickler?->name ?? $this->report->user->name;

        return new Content(
            view: 'emails.report-ready-for-sending',
            with: [
                'report' => $this->report,
                'manager' => $this->manager,
                'agency' => $agency,
                'entwicklerName' => $entwicklerName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

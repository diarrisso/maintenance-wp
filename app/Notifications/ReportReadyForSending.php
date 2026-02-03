<?php

namespace App\Notifications;

use App\Models\MaintenanceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReadyForSending extends Notification
{
    use Queueable;

    public function __construct(
        public MaintenanceReport $report
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $websiteName = $this->report->website->name;
        $clientName = $this->report->website->client->name;
        $entwicklerName = $this->report->entwickler?->name ?? $this->report->user->name;

        return (new MailMessage)
            ->subject("Wartungsbericht bereit zum Versand - {$websiteName}")
            ->greeting("Hallo {$notifiable->name},")
            ->line("Ein neuer Wartungsbericht ist bereit zum Versand an den Kunden.")
            ->line("**Website:** {$websiteName}")
            ->line("**Kunde:** {$clientName}")
            ->line("**Erstellt von:** {$entwicklerName}")
            ->line("**Datum:** " . $this->report->maintenance_date->format('d.m.Y'))
            ->action('Bericht ansehen und senden', route('reports.show', $this->report))
            ->line('Bitte prüfen Sie den Bericht und senden Sie ihn an den Kunden.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'website_name' => $this->report->website->name,
            'client_name' => $this->report->website->client->name,
        ];
    }
}

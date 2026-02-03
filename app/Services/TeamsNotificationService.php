<?php

namespace App\Services;

use App\Models\MaintenanceReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TeamsNotificationService
{
    public function sendReport(MaintenanceReport $report): bool
    {
        $webhookUrl = $report->website->teams_webhook_url;

        if (empty($webhookUrl)) {
            return false;
        }

        $report->load(['website.client', 'pluginUpdates', 'user']);

        $card = $this->buildAdaptiveCard($report);

        try {
            $response = Http::post($webhookUrl, $card);
            return $response->successful();
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    private function buildAdaptiveCard(MaintenanceReport $report): array
    {
        $checksCompleted = collect([
            $report->check_frontend,
            $report->check_navigation,
            $report->check_forms,
            $report->check_responsive,
            $report->check_admin_login,
            $report->check_media_upload,
            $report->check_no_errors,
            $report->check_ssl,
            $report->check_security,
        ])->filter()->count();

        $pluginsUpdated = $report->pluginUpdates->where('status', 'updated')->count();
        $pluginsSkipped = $report->pluginUpdates->where('status', '!=', 'updated')->count();

        return [
            'type' => 'message',
            'attachments' => [
                [
                    'contentType' => 'application/vnd.microsoft.card.adaptive',
                    'content' => [
                        '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
                        'type' => 'AdaptiveCard',
                        'version' => '1.4',
                        'body' => [
                            [
                                'type' => 'TextBlock',
                                'size' => 'Large',
                                'weight' => 'Bolder',
                                'text' => 'Wartungsbericht - ' . $report->website->name,
                                'wrap' => true,
                            ],
                            [
                                'type' => 'FactSet',
                                'facts' => [
                                    ['title' => 'Kunde', 'value' => $report->website->client->name],
                                    ['title' => 'Website', 'value' => $report->website->url],
                                    ['title' => 'Datum', 'value' => $report->maintenance_date->format('d.m.Y')],
                                    ['title' => 'Techniker', 'value' => $report->user->name ?? 'N/A'],
                                ],
                            ],
                            [
                                'type' => 'TextBlock',
                                'text' => '**Zusammenfassung**',
                                'wrap' => true,
                                'separator' => true,
                            ],
                            [
                                'type' => 'FactSet',
                                'facts' => [
                                    ['title' => 'Backup', 'value' => $report->backup_completed ? '✅ Erledigt' : '❌ Nicht erledigt'],
                                    ['title' => 'Prüfungen', 'value' => $checksCompleted . '/9 bestanden'],
                                    ['title' => 'Plugins aktualisiert', 'value' => $pluginsUpdated . ' von ' . $report->pluginUpdates->count()],
                                    ['title' => 'Plugins übersprungen', 'value' => (string)$pluginsSkipped],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

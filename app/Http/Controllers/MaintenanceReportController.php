<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\MaintenanceReport;
use App\Models\PluginUpdate;
use App\Mail\MaintenanceReportMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;
use Carbon\Carbon;
use App\Services\TeamsNotificationService;
use App\Mail\ReportReadyForSendingMail;
use App\Models\User;

class MaintenanceReportController extends Controller
{
    public function index()
    {
        $reports = MaintenanceReport::with(['website.client', 'user'])
            ->orderBy('maintenance_date', 'desc')
            ->paginate(20);

        return view('reports.index', compact('reports'));
    }

    public function show(MaintenanceReport $report)
    {
        $report->load(['website.client', 'user', 'pluginUpdates', 'recommendations']);

        return view('reports.show', compact('report'));
    }

    public function create(Website $website)
    {
        $website->load('client');

        return view('maintenance.create', compact('website'));
    }

    public function store(Request $request, Website $website)
    {
        $validated = $request->validate([
            'entwickler_id' => 'required|exists:entwicklers,id',
            'website_condition' => 'required|in:excellent,good,needs_improvement,critical',
            'website_condition_notes' => 'nullable|string',
            'maintenance_date' => 'required|date',
            'maintenance_number' => 'nullable|integer|in:1,2',
            'backup_completed' => 'boolean',
            'backup_datetime' => 'nullable|date',
            'php_compatible' => 'boolean',
            'wp_version_before' => 'nullable|string',
            'wp_version_after' => 'nullable|string',
            'theme_name' => 'nullable|string',
            'theme_version_before' => 'nullable|string',
            'theme_version_after' => 'nullable|string',
            'check_frontend' => 'boolean',
            'check_navigation' => 'boolean',
            'check_forms' => 'boolean',
            'check_responsive' => 'boolean',
            'check_admin_login' => 'boolean',
            'check_media_upload' => 'boolean',
            'check_no_errors' => 'boolean',
            'check_woocommerce' => 'nullable|boolean',
            'check_ssl' => 'boolean',
            'check_security' => 'boolean',
            'security_plugin' => 'nullable|string',
            'security_issues_count' => 'nullable|integer|min:0',
            'security_issues_details' => 'nullable|string',
            'firewall_status' => 'nullable|integer|in:0,25,50,75,100',
            'firewall_notes' => 'nullable|string',
            'brute_force_attacks_day' => 'nullable|integer|min:0',
            'brute_force_attacks_week' => 'nullable|integer|min:0',
            'security_actions_taken' => 'nullable|string',
            'loading_time' => 'nullable|numeric',
            'issues_found' => 'nullable|string',
            'recommendations_text' => 'nullable|string',
            'next_maintenance_date' => 'nullable|date',
            'plugins' => 'nullable|array',
            'plugins.*.name' => 'required|string',
            'plugins.*.version_before' => 'nullable|string',
            'plugins.*.version_after' => 'nullable|string',
            'plugins.*.status' => 'nullable|in:updated,skipped,no_access',
            'plugins.*.notes' => 'nullable|string',
            'recommendations' => 'nullable|array',
            'recommendations.*.type' => 'required|in:security,plugin,theme,performance,other',
            'recommendations.*.priority' => 'required|in:critical,high,medium,low',
            'recommendations.*.title' => 'required|string',
            'recommendations.*.description' => 'nullable|string',
            'recommendations.*.action' => 'nullable|in:replace,remove,update,configure,install',
            'recommendations.*.current_item' => 'nullable|string',
            'recommendations.*.suggested_item' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        $validated['website_id'] = $website->id;
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'draft';
        $validated['recommendations'] = $validated['recommendations_text'] ?? null;
        unset($validated['recommendations_text']);

        // Calculer automatiquement le numéro de maintenance pour les clients avec paquet 2x par mois
        if ($website->client->maintenance_type === '2x_monthly' && empty($validated['maintenance_number'])) {
            $validated['maintenance_number'] = $this->calculateMaintenanceNumber($website, $validated['maintenance_date']);
        }

        $report = MaintenanceReport::create($validated);

        if ($request->has('plugins')) {
            foreach ($request->plugins as $plugin) {
                if (!empty($plugin['name'])) {
                    $report->pluginUpdates()->create([
                        'plugin_name' => $plugin['name'],
                        'version_before' => $plugin['version_before'] ?? null,
                        'version_after' => $plugin['version_after'] ?? null,
                        'status' => $plugin['status'] ?? 'updated',
                        'notes' => $plugin['notes'] ?? null,
                    ]);
                }
            }
        }

        if ($request->has('recommendations')) {
            foreach ($request->recommendations as $rec) {
                if (!empty($rec['title'])) {
                    $report->recommendations()->create([
                        'type' => $rec['type'],
                        'priority' => $rec['priority'],
                        'title' => $rec['title'],
                        'description' => $rec['description'] ?? null,
                        'action' => $rec['action'] ?? null,
                        'current_item' => $rec['current_item'] ?? null,
                        'suggested_item' => $rec['suggested_item'] ?? null,
                    ]);
                }
            }
        }

        notify()->success('Wartungsbericht als Entwurf gespeichert.');
        return redirect()->route('maintenance.edit', $report);
    }

    public function edit(MaintenanceReport $report)
    {
        $report->load(['website.client', 'pluginUpdates', 'recommendations']);

        return view('maintenance.edit', compact('report'));
    }

    public function update(Request $request, MaintenanceReport $report)
    {
        $validated = $request->validate([
            'entwickler_id' => 'required|exists:entwicklers,id',
            'website_condition' => 'required|in:excellent,good,needs_improvement,critical',
            'website_condition_notes' => 'nullable|string',
            'maintenance_date' => 'required|date',
            'maintenance_number' => 'nullable|integer|in:1,2',
            'backup_completed' => 'boolean',
            'backup_datetime' => 'nullable|date',
            'php_compatible' => 'boolean',
            'wp_version_before' => 'nullable|string',
            'wp_version_after' => 'nullable|string',
            'theme_name' => 'nullable|string',
            'theme_version_before' => 'nullable|string',
            'theme_version_after' => 'nullable|string',
            'check_frontend' => 'boolean',
            'check_navigation' => 'boolean',
            'check_forms' => 'boolean',
            'check_responsive' => 'boolean',
            'check_admin_login' => 'boolean',
            'check_media_upload' => 'boolean',
            'check_no_errors' => 'boolean',
            'check_woocommerce' => 'nullable|boolean',
            'check_ssl' => 'boolean',
            'check_security' => 'boolean',
            'security_plugin' => 'nullable|string',
            'security_issues_count' => 'nullable|integer|min:0',
            'security_issues_details' => 'nullable|string',
            'firewall_status' => 'nullable|integer|in:0,25,50,75,100',
            'firewall_notes' => 'nullable|string',
            'brute_force_attacks_day' => 'nullable|integer|min:0',
            'brute_force_attacks_week' => 'nullable|integer|min:0',
            'security_actions_taken' => 'nullable|string',
            'loading_time' => 'nullable|numeric',
            'issues_found' => 'nullable|string',
            'recommendations_text' => 'nullable|string',
            'next_maintenance_date' => 'nullable|date',
            'plugins' => 'nullable|array',
            'plugins.*.name' => 'required|string',
            'plugins.*.version_before' => 'nullable|string',
            'plugins.*.version_after' => 'nullable|string',
            'plugins.*.status' => 'nullable|in:updated,skipped,no_access',
            'plugins.*.notes' => 'nullable|string',
            'recommendations' => 'nullable|array',
            'recommendations.*.type' => 'required|in:security,plugin,theme,performance,other',
            'recommendations.*.priority' => 'required|in:critical,high,medium,low',
            'recommendations.*.title' => 'required|string',
            'recommendations.*.description' => 'nullable|string',
            'recommendations.*.action' => 'nullable|in:replace,remove,update,configure,install',
            'recommendations.*.current_item' => 'nullable|string',
            'recommendations.*.suggested_item' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        $validated['recommendations'] = $validated['recommendations_text'] ?? null;
        unset($validated['recommendations_text']);

        // Calculer automatiquement le numéro de maintenance pour les clients avec paquet 2x par mois
        if ($report->website->client->maintenance_type === '2x_monthly' && empty($validated['maintenance_number'])) {
            $validated['maintenance_number'] = $this->calculateMaintenanceNumber($report->website, $validated['maintenance_date']);
        }

        $report->update($validated);

        $report->pluginUpdates()->delete();
        if ($request->has('plugins')) {
            foreach ($request->plugins as $plugin) {
                if (!empty($plugin['name'])) {
                    $report->pluginUpdates()->create([
                        'plugin_name' => $plugin['name'],
                        'version_before' => $plugin['version_before'] ?? null,
                        'version_after' => $plugin['version_after'] ?? null,
                        'status' => $plugin['status'] ?? 'updated',
                        'notes' => $plugin['notes'] ?? null,
                    ]);
                }
            }
        }

        $report->recommendations()->delete();
        if ($request->has('recommendations')) {
            foreach ($request->recommendations as $rec) {
                if (!empty($rec['title'])) {
                    $report->recommendations()->create([
                        'type' => $rec['type'],
                        'priority' => $rec['priority'],
                        'title' => $rec['title'],
                        'description' => $rec['description'] ?? null,
                        'action' => $rec['action'] ?? null,
                        'current_item' => $rec['current_item'] ?? null,
                        'suggested_item' => $rec['suggested_item'] ?? null,
                    ]);
                }
            }
        }

        notify()->success('Wartungsbericht aktualisiert.');
        return redirect()->route('reports.show', $report);
    }

    public function complete(MaintenanceReport $report)
    {
        if ($report->status === 'completed' || $report->status === 'sent') {
            notify()->warning('Dieser Bericht wurde bereits abgeschlossen.');
            return redirect()->route('reports.show', $report);
        }

        $report->load(['website.client', 'pluginUpdates', 'recommendations', 'user', 'entwickler']);

        $filename = 'wartungsbericht_' . $report->website->name . '_' . $report->maintenance_date->format('Y-m-d') . '.pdf';
        $path = 'reports/' . $filename;

        Pdf::view('pdf.maintenance-report', compact('report'))
            ->format('a4')
            ->save(storage_path('app/public/' . $path));

        $report->update([
            'status' => 'completed',
            'pdf_path' => $path,
        ]);

        $nextDate = $this->calculateNextMaintenanceDate($report->website);
        $report->website->update([
            'next_maintenance_date' => $nextDate,
        ]);

        // Notifier tous les Managers que le rapport est prêt à être envoyé
        $managers = User::where('role', User::ROLE_MANAGER)->get();
        foreach ($managers as $manager) {
            Mail::to($manager->email)->send(new ReportReadyForSendingMail($report, $manager));
        }

        notify()->success('Wartungsbericht abgeschlossen. PDF wurde erstellt. Manager wurden benachrichtigt.');
        return redirect()->route('reports.show', $report);
    }

    public function sendEmail(MaintenanceReport $report)
    {
        if (!in_array($report->status, ['completed', 'sent'])) {
            notify()->error('Bericht muss zuerst abgeschlossen werden.');
            return redirect()->route('reports.show', $report);
        }

        $report->load(['website.client', 'website.projectManager']);

        $messages = [];

        // Send to client
        try {
            Mail::to($report->website->client->email)->send(new MaintenanceReportMail($report));
            $messages[] = 'E-Mail an Kunde gesendet';
        } catch (\Exception $e) {
            $messages[] = 'E-Mail an Kunde fehlgeschlagen: ' . $e->getMessage();
        }

        // Send to PM if configured (user or email)
        $pmEmail = $report->website->projectManager?->email ?? $report->website->pm_email;
        if ($pmEmail) {
            try {
                Mail::to($pmEmail)->send(new MaintenanceReportMail($report));
                $pmName = $report->website->projectManager?->name ?? $pmEmail;
                $messages[] = "E-Mail an PM ({$pmName}) gesendet";
            } catch (\Exception $e) {
                $messages[] = 'E-Mail an PM fehlgeschlagen: ' . $e->getMessage();
            }
        }

        // Update status to sent
        $report->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        notify()->success(implode(', ', $messages));
        return redirect()->route('reports.show', $report);
    }

    public function sendTeams(MaintenanceReport $report)
    {
        if (!in_array($report->status, ['completed', 'sent'])) {
            notify()->error('Bericht muss zuerst abgeschlossen werden.');
            return redirect()->route('reports.show', $report);
        }

        if (!$report->website->teams_webhook_url) {
            notify()->error('Keine Teams Webhook URL konfiguriert.');
            return redirect()->route('reports.show', $report);
        }

        $teamsService = new TeamsNotificationService();
        if ($teamsService->sendReport($report)) {
            notify()->success('Teams-Benachrichtigung gesendet.');
            return redirect()->route('reports.show', $report);
        }

        notify()->error('Teams-Benachrichtigung fehlgeschlagen.');
        return redirect()->route('reports.show', $report);
    }

    public function download(MaintenanceReport $report)
    {
        if (!$report->pdf_path || !Storage::disk('public')->exists($report->pdf_path)) {
            abort(404, 'PDF-Datei nicht gefunden.');
        }

        return Storage::disk('public')->download($report->pdf_path);
    }

    public function regeneratePdf(MaintenanceReport $report)
    {
        if (!in_array($report->status, ['completed', 'sent'])) {
            notify()->error('Nur abgeschlossene Berichte können neu generiert werden.');
            return redirect()->route('reports.show', $report);
        }

        $report->load(['website.client', 'pluginUpdates', 'recommendations', 'user', 'entwickler']);

        $filename = 'wartungsbericht_' . $report->website->name . '_' . $report->maintenance_date->format('Y-m-d') . '.pdf';
        $path = 'reports/' . $filename;

        Pdf::view('pdf.maintenance-report', compact('report'))
            ->format('a4')
            ->save(storage_path('app/public/' . $path));

        $report->update(['pdf_path' => $path]);

        notify()->success('PDF wurde neu generiert.');
        return redirect()->route('reports.show', $report);
    }

    public function resend(MaintenanceReport $report)
    {
        if ($report->status !== 'sent') {
            notify()->error('Nur abgeschlossene Berichte können erneut gesendet werden.');
            return redirect()->route('reports.show', $report);
        }

        try {
            Mail::to($report->website->client->email)->send(new MaintenanceReportMail($report));
            notify()->success('Bericht wurde erneut verschickt.');
            return redirect()->route('reports.show', $report);
        } catch (\Exception $e) {
            notify()->error('E-Mail konnte nicht gesendet werden: ' . $e->getMessage());
            return redirect()->route('reports.show', $report);
        }
    }

    public function duplicate(MaintenanceReport $report)
    {
        $report->load(['pluginUpdates', 'recommendations']);

        $newReport = $report->replicate([
            'status', 'pdf_path', 'sent_at', 'signature', 'maintenance_number',
        ]);
        $newReport->status = 'draft';
        $newReport->maintenance_date = now();
        $newReport->user_id = Auth::id();
        $newReport->save();

        foreach ($report->pluginUpdates as $plugin) {
            $newReport->pluginUpdates()->create($plugin->only([
                'plugin_name', 'version_before', 'version_after', 'status', 'notes',
            ]));
        }

        foreach ($report->recommendations()->get() as $rec) {
            $newReport->recommendations()->create($rec->only([
                'type', 'priority', 'title', 'description', 'action',
                'current_item', 'suggested_item',
            ]));
        }

        notify()->success('Bericht wurde dupliziert.');
        return redirect()->route('maintenance.edit', $newReport);
    }

    public function destroy(MaintenanceReport $report)
    {
        // Supprimer le PDF s'il existe
        if ($report->pdf_path && Storage::disk('public')->exists($report->pdf_path)) {
            Storage::disk('public')->delete($report->pdf_path);
        }

        $websiteId = $report->website_id;
        $report->recommendations()->delete();
        $report->pluginUpdates()->delete();
        $report->delete();

        notify()->success('Wartungsbericht wurde gelöscht.');
        return redirect()->route('websites.show', $websiteId);
    }

    private function calculateNextMaintenanceDate(Website $website)
    {
        $today = Carbon::today();

        // Gérer le cas spécial 2x par mois
        if ($website->client->maintenance_type === '2x_monthly') {
            // Vérifier la dernière maintenance
            $lastReport = MaintenanceReport::where('website_id', $website->id)
                ->orderBy('maintenance_date', 'desc')
                ->first();

            if ($lastReport && $lastReport->maintenance_number === 1) {
                // Si c'était la 1ère maintenance (début du mois) → prochaine = 15 du même mois
                return $lastReport->maintenance_date->copy()->setDay(15);
            } else {
                // Si c'était la 2ème maintenance (milieu du mois) → prochaine = 1er du mois suivant
                return $today->addMonth()->startOfMonth();
            }
        }

        return match ($website->maintenance_package) {
            'monthly' => $today->addMonth(),
            'quarterly' => $today->addMonths(3),
            'yearly' => $today->addYear(),
            default => null,
        };
    }

    /**
     * Calcule automatiquement le numéro de maintenance (1 ou 2) pour les clients avec paquet 2x par mois
     * en fonction de la date dans le mois :
     * - Avant le 15 du mois → 1ère Wartung
     * - À partir du 15 du mois → 2ème Wartung
     */
    private function calculateMaintenanceNumber(Website $website, $maintenanceDate): int
    {
        $date = Carbon::parse($maintenanceDate);

        // Si la date est avant le 15 du mois → 1ère wartung
        // Si la date est le 15 ou après → 2ème wartung
        return $date->day < 15 ? 1 : 2;
    }
}

<?php

namespace Database\Seeders;

use App\Models\MaintenanceReport;
use App\Models\PluginUpdate;
use App\Models\User;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MaintenanceReportSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $websites = Website::all();

        // Rapports envoyés (historique)
        $this->createSentReports($users, $websites);

        // Rapport complété (non envoyé)
        $this->createCompletedReport($users, $websites);

        // Rapport en brouillon
        $this->createDraftReport($users, $websites);
    }

    private function createSentReports($users, $websites): void
    {
        $sentReports = [
            [
                'website_name' => 'Müller GmbH Hauptseite',
                'days_ago' => 30,
                'wp_before' => '6.4.1',
                'wp_after' => '6.4.2',
                'theme' => 'Flavor Theme',
                'issues' => null,
                'recommendations' => 'PHP-Version auf 8.3 aktualisieren empfohlen.',
            ],
            [
                'website_name' => 'Müller GmbH Hauptseite',
                'days_ago' => 60,
                'wp_before' => '6.3.2',
                'wp_after' => '6.4.1',
                'theme' => 'Flavor Theme',
                'issues' => 'Kontaktformular temporär nicht erreichbar.',
                'recommendations' => 'Contact Form 7 Plugin aktualisiert, Problem behoben.',
            ],
            [
                'website_name' => 'Becker Design Studio',
                'days_ago' => 25,
                'wp_before' => '6.4.2',
                'wp_after' => '6.5.0',
                'theme' => 'Flavor Theme',
                'issues' => null,
                'recommendations' => 'Bilder optimieren für bessere Ladezeit.',
            ],
            [
                'website_name' => 'Schmidt & Partner',
                'days_ago' => 90,
                'wp_before' => '6.3.0',
                'wp_after' => '6.4.1',
                'theme' => 'Flavor Theme',
                'issues' => 'SSL-Zertifikat läuft in 30 Tagen ab.',
                'recommendations' => 'SSL-Zertifikat rechtzeitig erneuern.',
            ],
            [
                'website_name' => 'Hotel Bergblick',
                'days_ago' => 15,
                'wp_before' => '6.4.2',
                'wp_after' => '6.5.0',
                'theme' => 'flavor Theme',
                'issues' => null,
                'recommendations' => 'Buchungssystem funktioniert einwandfrei.',
            ],
        ];

        foreach ($sentReports as $data) {
            $website = $websites->firstWhere('name', $data['website_name']);
            if (!$website) {
                continue;
            }

            $report = MaintenanceReport::create([
                'user_id' => $users->random()->id,
                'website_id' => $website->id,
                'maintenance_date' => Carbon::now()->subDays($data['days_ago']),
                'status' => 'sent',
                'backup_completed' => true,
                'backup_datetime' => Carbon::now()->subDays($data['days_ago'])->setTime(9, 0),
                'php_compatible' => true,
                'wp_version_before' => $data['wp_before'],
                'wp_version_after' => $data['wp_after'],
                'theme_name' => $data['theme'],
                'theme_version_before' => '2.0.0',
                'theme_version_after' => '2.1.0',
                'check_frontend' => true,
                'check_navigation' => true,
                'check_forms' => true,
                'check_responsive' => true,
                'check_admin_login' => true,
                'check_media_upload' => true,
                'check_no_errors' => true,
                'check_woocommerce' => false,
                'check_ssl' => true,
                'check_security' => true,
                'loading_time' => rand(10, 25) / 10,
                'issues_found' => $data['issues'],
                'recommendations' => $data['recommendations'],
                'next_maintenance_date' => Carbon::now()->subDays($data['days_ago'])->addMonth(),
                'sent_at' => Carbon::now()->subDays($data['days_ago'])->setTime(16, 30),
                'pdf_path' => 'reports/report-' . $website->id . '-' . Carbon::now()->subDays($data['days_ago'])->format('Y-m-d') . '.pdf',
            ]);

            $this->createPluginUpdates($report);
        }
    }

    private function createCompletedReport($users, $websites): void
    {
        $website = $websites->firstWhere('name', 'Braun Immobilien');
        if (!$website) {
            return;
        }

        $report = MaintenanceReport::create([
            'user_id' => $users->first()->id,
            'website_id' => $website->id,
            'maintenance_date' => Carbon::today(),
            'status' => 'completed',
            'backup_completed' => true,
            'backup_datetime' => Carbon::today()->setTime(10, 0),
            'php_compatible' => true,
            'wp_version_before' => '6.4.1',
            'wp_version_after' => '6.4.2',
            'theme_name' => 'flavor Theme',
            'theme_version_before' => '1.8.0',
            'theme_version_after' => '1.9.0',
            'check_frontend' => true,
            'check_navigation' => true,
            'check_forms' => true,
            'check_responsive' => true,
            'check_admin_login' => true,
            'check_media_upload' => true,
            'check_no_errors' => true,
            'check_woocommerce' => false,
            'check_ssl' => true,
            'check_security' => true,
            'loading_time' => 1.8,
            'issues_found' => null,
            'recommendations' => 'Website läuft stabil. Nächste Wartung planmäßig.',
            'next_maintenance_date' => Carbon::today()->addMonth(),
            'sent_at' => null,
            'pdf_path' => null,
        ]);

        $this->createPluginUpdates($report);
    }

    private function createDraftReport($users, $websites): void
    {
        $website = $websites->firstWhere('name', 'Fitness Zone');
        if (!$website) {
            return;
        }

        MaintenanceReport::create([
            'user_id' => $users->first()->id,
            'website_id' => $website->id,
            'maintenance_date' => Carbon::today(),
            'status' => 'draft',
            'backup_completed' => true,
            'backup_datetime' => Carbon::today()->setTime(8, 30),
            'php_compatible' => true,
            'wp_version_before' => '6.4.1',
            'wp_version_after' => null,
            'theme_name' => null,
            'theme_version_before' => null,
            'theme_version_after' => null,
            'check_frontend' => false,
            'check_navigation' => false,
            'check_forms' => false,
            'check_responsive' => false,
            'check_admin_login' => false,
            'check_media_upload' => false,
            'check_no_errors' => false,
            'check_woocommerce' => false,
            'check_ssl' => false,
            'check_security' => false,
            'loading_time' => null,
            'issues_found' => null,
            'recommendations' => null,
            'next_maintenance_date' => null,
            'sent_at' => null,
            'pdf_path' => null,
        ]);
    }

    private function createPluginUpdates(MaintenanceReport $report): void
    {
        $plugins = [
            ['name' => 'Yoast SEO', 'before' => '21.5', 'after' => '21.6'],
            ['name' => 'Contact Form 7', 'before' => '5.8.4', 'after' => '5.8.5'],
            ['name' => 'Wordfence Security', 'before' => '7.10.5', 'after' => '7.11.0'],
            ['name' => 'WP Rocket', 'before' => '3.14', 'after' => '3.15'],
            ['name' => 'Advanced Custom Fields', 'before' => '6.2.4', 'after' => '6.2.5'],
            ['name' => 'Elementor', 'before' => '3.18.0', 'after' => '3.19.0'],
            ['name' => 'UpdraftPlus', 'before' => '1.23.10', 'after' => '1.23.12'],
        ];

        // 2-4 plugins aléatoires par rapport
        $selectedPlugins = collect($plugins)->random(rand(2, 4));

        foreach ($selectedPlugins as $plugin) {
            PluginUpdate::create([
                'maintenance_report_id' => $report->id,
                'plugin_name' => $plugin['name'],
                'version_before' => $plugin['before'],
                'version_after' => $plugin['after'],
            ]);
        }
    }
}

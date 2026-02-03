<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $websites = [
            // Client 1 - Müller GmbH (2 sites)
            [
                'client_email' => 'info@mueller-gmbh.de',
                'name' => 'Müller GmbH Hauptseite',
                'url' => 'https://mueller-gmbh.de',
                'hosting_provider' => 'Hetzner',
                'php_version' => '8.2',
                'wordpress_version' => '6.4.2',
                'admin_url' => 'https://mueller-gmbh.de/wp-admin',
                'maintenance_package' => 'monthly',
                'next_maintenance_date' => Carbon::now()->addDays(5),
            ],
            [
                'client_email' => 'info@mueller-gmbh.de',
                'name' => 'Müller Shop',
                'url' => 'https://shop.mueller-gmbh.de',
                'hosting_provider' => 'Hetzner',
                'php_version' => '8.2',
                'wordpress_version' => '6.4.2',
                'admin_url' => 'https://shop.mueller-gmbh.de/wp-admin',
                'maintenance_package' => 'monthly',
                'next_maintenance_date' => Carbon::now()->addDays(5),
            ],
            // Client 2 - Schmidt & Partner
            [
                'client_email' => 'kontakt@schmidt-partner.de',
                'name' => 'Schmidt & Partner',
                'url' => 'https://schmidt-partner.de',
                'hosting_provider' => 'Strato',
                'php_version' => '8.1',
                'wordpress_version' => '6.4.1',
                'admin_url' => 'https://schmidt-partner.de/wp-admin',
                'maintenance_package' => 'quarterly',
                'next_maintenance_date' => Carbon::now()->addDays(45),
            ],
            // Client 3 - Wagner Bau (overdue)
            [
                'client_email' => 'peter@wagner-bau.de',
                'name' => 'Wagner Bau',
                'url' => 'https://wagner-bau.de',
                'hosting_provider' => 'IONOS',
                'php_version' => '8.2',
                'wordpress_version' => '6.4.2',
                'admin_url' => 'https://wagner-bau.de/wp-admin',
                'maintenance_package' => 'monthly',
                'next_maintenance_date' => Carbon::now()->subDays(3), // Überfällig
            ],
            // Client 4 - Becker Design
            [
                'client_email' => 'info@becker-design.de',
                'name' => 'Becker Design Studio',
                'url' => 'https://becker-design.de',
                'hosting_provider' => 'DigitalOcean',
                'php_version' => '8.3',
                'wordpress_version' => '6.5.0',
                'admin_url' => 'https://becker-design.de/wp-admin',
                'maintenance_package' => 'monthly',
                'next_maintenance_date' => Carbon::now()->addDays(12),
            ],
            // Client 5 - Autohaus Fischer
            [
                'client_email' => 'mfischer@autohaus-fischer.de',
                'name' => 'Autohaus Fischer',
                'url' => 'https://autohaus-fischer.de',
                'hosting_provider' => 'AWS',
                'php_version' => '8.2',
                'wordpress_version' => '6.4.2',
                'admin_url' => 'https://autohaus-fischer.de/wp-admin',
                'maintenance_package' => 'quarterly',
                'next_maintenance_date' => Carbon::now()->addDays(60),
            ],
            // Client 6 - Zahnarztpraxis (overdue)
            [
                'client_email' => 'laura@praxis-hoffmann.de',
                'name' => 'Zahnarztpraxis Dr. Hoffmann',
                'url' => 'https://praxis-hoffmann.de',
                'hosting_provider' => 'Hetzner',
                'php_version' => '8.1',
                'wordpress_version' => '6.4.1',
                'admin_url' => 'https://praxis-hoffmann.de/wp-admin',
                'maintenance_package' => 'monthly',
                'next_maintenance_date' => Carbon::now()->subDays(7), // Überfällig
            ],
            // Client 7 - Restaurant
            [
                'client_email' => 'koch@restaurant-goldener-loewe.de',
                'name' => 'Restaurant Goldener Löwe',
                'url' => 'https://goldener-loewe.de',
                'hosting_provider' => 'Strato',
                'php_version' => '8.2',
                'wordpress_version' => '6.4.2',
                'admin_url' => 'https://goldener-loewe.de/wp-admin',
                'maintenance_package' => 'yearly',
                'next_maintenance_date' => Carbon::now()->addDays(120),
            ],
            // Client 8 - Immobilien
            [
                'client_email' => 'info@braun-immobilien.de',
                'name' => 'Braun Immobilien',
                'url' => 'https://braun-immobilien.de',
                'hosting_provider' => 'IONOS',
                'php_version' => '8.2',
                'wordpress_version' => '6.4.2',
                'admin_url' => 'https://braun-immobilien.de/wp-admin',
                'maintenance_package' => 'monthly',
                'next_maintenance_date' => Carbon::now()->addDays(2),
            ],
            // Client 9 - Fitness (upcoming)
            [
                'client_email' => 'az@fitness-zone.de',
                'name' => 'Fitness Zone',
                'url' => 'https://fitness-zone.de',
                'hosting_provider' => 'DigitalOcean',
                'php_version' => '8.2',
                'wordpress_version' => '6.4.2',
                'admin_url' => 'https://fitness-zone.de/wp-admin',
                'maintenance_package' => 'quarterly',
                'next_maintenance_date' => Carbon::now()->addDays(3),
            ],
            // Client 10 - Hotel
            [
                'client_email' => 'kontakt@hotel-bergblick.de',
                'name' => 'Hotel Bergblick',
                'url' => 'https://hotel-bergblick.de',
                'hosting_provider' => 'AWS',
                'php_version' => '8.3',
                'wordpress_version' => '6.5.0',
                'admin_url' => 'https://hotel-bergblick.de/wp-admin',
                'maintenance_package' => 'monthly',
                'next_maintenance_date' => Carbon::now()->addDays(18),
            ],
        ];

        foreach ($websites as $data) {
            $client = Client::where('email', $data['client_email'])->first();

            if ($client) {
                unset($data['client_email']);
                Website::create(array_merge($data, ['client_id' => $client->id]));
            }
        }
    }
}

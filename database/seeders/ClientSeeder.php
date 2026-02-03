<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Hans Müller',
                'email' => 'info@mueller-gmbh.de',
                'phone' => '+49 30 12345678',
                'company' => 'Müller GmbH',
                'notes' => 'Wichtiger Kunde seit 2020. Bevorzugt Wartung am Wochenende.',
            ],
            [
                'name' => 'Sabine Schmidt',
                'email' => 'kontakt@schmidt-partner.de',
                'phone' => '+49 40 98765432',
                'company' => 'Schmidt & Partner Rechtsanwälte',
                'notes' => 'Rechtsanwaltskanzlei mit hohen Sicherheitsanforderungen.',
            ],
            [
                'name' => 'Peter Wagner',
                'email' => 'peter@wagner-bau.de',
                'phone' => '+49 89 55443322',
                'company' => 'Wagner Bau GmbH',
                'notes' => 'Bauunternehmen mit WooCommerce-Shop für Baumaterialien.',
            ],
            [
                'name' => 'Claudia Becker',
                'email' => 'info@becker-design.de',
                'phone' => '+49 221 11223344',
                'company' => 'Becker Design Studio',
                'notes' => 'Kreativagentur. Website mit vielen Medien und Portfolio.',
            ],
            [
                'name' => 'Michael Fischer',
                'email' => 'mfischer@autohaus-fischer.de',
                'phone' => '+49 711 99887766',
                'company' => 'Autohaus Fischer',
                'notes' => 'Autohaus mit Fahrzeugbörse-Integration.',
            ],
            [
                'name' => 'Laura Hoffmann',
                'email' => 'laura@praxis-hoffmann.de',
                'phone' => '+49 69 44556677',
                'company' => 'Zahnarztpraxis Dr. Hoffmann',
                'notes' => 'Medizinische Praxis. DSGVO besonders wichtig.',
            ],
            [
                'name' => 'Stefan Koch',
                'email' => 'koch@restaurant-goldener-loewe.de',
                'phone' => '+49 351 22334455',
                'company' => 'Restaurant Goldener Löwe',
                'notes' => 'Restaurant mit Online-Reservierung und Speisekarte.',
            ],
            [
                'name' => 'Julia Braun',
                'email' => 'info@braun-immobilien.de',
                'phone' => '+49 511 66778899',
                'company' => 'Braun Immobilien',
                'notes' => 'Immobilienmakler mit IDX-Integration.',
            ],
            [
                'name' => 'Andreas Zimmermann',
                'email' => 'az@fitness-zone.de',
                'phone' => '+49 201 33445566',
                'company' => 'Fitness Zone',
                'notes' => 'Fitnessstudio mit Kursplan und Mitgliederbereich.',
            ],
            [
                'name' => 'Monika Schulz',
                'email' => 'kontakt@hotel-bergblick.de',
                'phone' => '+49 8821 77889900',
                'company' => 'Hotel Bergblick',
                'notes' => 'Hotel mit Buchungssystem. Peak-Zeiten beachten.',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}

<?php

namespace Database\Factories;

use App\Models\MaintenanceReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class PluginUpdateFactory extends Factory
{
    public function definition(): array
    {
        $plugins = [
            'WooCommerce',
            'Yoast SEO',
            'Contact Form 7',
            'Elementor',
            'Wordfence',
            'WP Rocket',
            'Advanced Custom Fields',
        ];

        return [
            'maintenance_report_id' => MaintenanceReport::factory(),
            'plugin_name' => fake()->randomElement($plugins),
            'version_before' => fake()->numerify('#.#.#'),
            'version_after' => fake()->numerify('#.#.#'),
        ];
    }
}

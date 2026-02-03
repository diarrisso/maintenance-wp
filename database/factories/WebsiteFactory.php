<?php

namespace Database\Factories;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebsiteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->company() . ' Website',
            'url' => fake()->url(),
            'hosting_provider' => fake()->randomElement(['Hetzner', 'Strato', 'AWS', 'DigitalOcean']),
            'php_version' => fake()->randomElement(['8.1', '8.2', '8.3']),
            'wordpress_version' => fake()->randomElement(['6.4.1', '6.4.2', '6.5.0']),
            'admin_url' => fake()->url() . '/wp-admin',
            'maintenance_package' => fake()->randomElement(['monthly', 'quarterly', 'yearly', 'one_time']),
            'next_maintenance_date' => Carbon::now()->addDays(fake()->numberBetween(1, 90)),
        ];
    }
}

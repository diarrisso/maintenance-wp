<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'website_id' => Website::factory(),
            'maintenance_date' => Carbon::today(),
            'status' => fake()->randomElement(['draft', 'completed', 'sent']),
            'backup_completed' => fake()->boolean(80),
            'backup_datetime' => Carbon::now()->subHours(fake()->numberBetween(1, 24)),
            'php_compatible' => fake()->boolean(90),
            'wp_version_before' => '6.4.1',
            'wp_version_after' => '6.4.2',
            'theme_name' => fake()->word() . ' Theme',
            'theme_version_before' => '1.0.0',
            'theme_version_after' => '1.1.0',
            'check_frontend' => fake()->boolean(95),
            'check_navigation' => fake()->boolean(95),
            'check_forms' => fake()->boolean(90),
            'check_responsive' => fake()->boolean(95),
            'check_admin_login' => fake()->boolean(95),
            'check_media_upload' => fake()->boolean(90),
            'check_no_errors' => fake()->boolean(85),
            'check_woocommerce' => fake()->boolean(70),
            'check_ssl' => fake()->boolean(95),
            'check_security' => fake()->boolean(90),
            'loading_time' => fake()->randomFloat(2, 0.5, 3.0),
            'issues_found' => fake()->optional()->paragraph(),
            'recommendations' => fake()->optional()->paragraph(),
            'next_maintenance_date' => Carbon::now()->addMonth(),
            'sent_at' => null,
            'pdf_path' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'sent_at' => null,
            'pdf_path' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => Carbon::now(),
            'pdf_path' => 'reports/test-report.pdf',
        ]);
    }
}

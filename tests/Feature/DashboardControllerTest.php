<?php

use App\Models\User;
use App\Models\Client;
use App\Models\Website;
use App\Models\MaintenanceReport;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertViewIs('dashboard')
        ->assertViewHas(['clientsCount', 'websitesCount', 'upcomingMaintenances', 'overdueMaintenances', 'recentReports']);
});

test('dashboard shows correct client count', function () {
    Client::factory()->count(5)->create();

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertViewHas('clientsCount', 5);
});

test('dashboard shows correct website count', function () {
    Website::factory()->count(8)->create();

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertViewHas('websitesCount', 8);
});

test('dashboard shows upcoming maintenances', function () {
    $website = Website::factory()->create([
        'name' => 'Upcoming Website',
        'next_maintenance_date' => Carbon::now()->addDays(3),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertSee('Upcoming Website');
});

test('dashboard shows overdue maintenances', function () {
    $website = Website::factory()->create([
        'name' => 'Overdue Website',
        'next_maintenance_date' => Carbon::now()->subDays(3),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertSee('Overdue Website');
});

test('dashboard shows recent reports', function () {
    $report = MaintenanceReport::factory()->create([
        'maintenance_date' => Carbon::now(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertSee($report->website->name);
});

test('guest cannot access dashboard', function () {
    Auth::logout();

    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

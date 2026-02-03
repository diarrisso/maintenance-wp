<?php

use App\Models\User;
use App\Models\Website;
use App\Models\MaintenanceReport;
use App\Models\PluginUpdate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Storage::fake('public');
    Mail::fake();
});

test('can create a maintenance report from website', function () {
    $website = Website::factory()->create();

    $response = $this->post(route('maintenance.store', $website), [
        'maintenance_date' => Carbon::today()->format('Y-m-d'),
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('maintenance_reports', [
        'website_id' => $website->id,
        'user_id' => $this->user->id,
        'status' => 'draft',
    ]);
});

test('can view edit maintenance report page', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    $response = $this->get(route('maintenance.edit', $report));

    $response->assertStatus(200)
        ->assertViewIs('maintenance.edit')
        ->assertViewHas('report');
});

test('can update a maintenance report', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    $updateData = [
        'maintenance_date' => Carbon::today()->format('Y-m-d'),
        'backup_completed' => '1',
        'backup_datetime' => Carbon::now()->format('Y-m-d\TH:i'),
        'php_compatible' => '1',
        'wp_version_before' => '6.4.1',
        'wp_version_after' => '6.4.2',
        'check_frontend' => '1',
        'check_navigation' => '1',
        'issues_found' => 'No issues',
        'recommendations' => 'All good',
        'plugins' => [
            ['name' => 'WooCommerce', 'version_before' => '8.0.0', 'version_after' => '8.1.0'],
        ],
    ];

    $response = $this->put(route('maintenance.update', $report), $updateData);

    $response->assertRedirect(route('reports.show', $report))
        ->assertSessionHas('success');

    $report->refresh();

    expect($report->backup_completed)->toBeTrue()
        ->and($report->php_compatible)->toBeTrue()
        ->and($report->check_frontend)->toBeTrue()
        ->and($report->wp_version_before)->toBe('6.4.1')
        ->and($report->wp_version_after)->toBe('6.4.2');
});

test('can add plugin updates when updating maintenance report', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    $updateData = [
        'maintenance_date' => Carbon::today()->format('Y-m-d'),
        'plugins' => [
            ['name' => 'WooCommerce', 'version_before' => '8.0.0', 'version_after' => '8.1.0'],
            ['name' => 'Yoast SEO', 'version_before' => '20.0', 'version_after' => '20.1'],
        ],
    ];

    $this->put(route('maintenance.update', $report), $updateData);

    $report->refresh();

    expect($report->pluginUpdates)->toHaveCount(2)
        ->and($report->pluginUpdates->where('plugin_name', 'WooCommerce')->first())->not->toBeNull()
        ->and($report->pluginUpdates->where('plugin_name', 'Yoast SEO')->first())->not->toBeNull();
});

test('plugin updates are replaced when updating report', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    PluginUpdate::create([
        'maintenance_report_id' => $report->id,
        'plugin_name' => 'Old Plugin',
        'version_before' => '1.0.0',
        'version_after' => '1.1.0',
    ]);

    $updateData = [
        'maintenance_date' => Carbon::today()->format('Y-m-d'),
        'plugins' => [
            ['name' => 'New Plugin', 'version_before' => '2.0.0', 'version_after' => '2.1.0'],
        ],
    ];

    $this->put(route('maintenance.update', $report), $updateData);

    $report->refresh();

    expect($report->pluginUpdates)->toHaveCount(1)
        ->and($report->pluginUpdates->first()->plugin_name)->toBe('New Plugin')
        ->and($report->pluginUpdates->where('plugin_name', 'Old Plugin')->count())->toBe(0);
});

test('can view maintenance report', function () {
    $report = MaintenanceReport::factory()->completed()->create();

    $response = $this->get(route('reports.show', $report));

    $response->assertStatus(200)
        ->assertViewIs('reports.show')
        ->assertViewHas('report');
});

test('can view reports index page', function () {
    MaintenanceReport::factory()->count(3)->create();

    $response = $this->get(route('reports.index'));

    $response->assertStatus(200)
        ->assertViewIs('reports.index')
        ->assertViewHas('reports');
});

test('next maintenance date is calculated correctly for monthly package', function () {
    $website = Website::factory()->create([
        'maintenance_package' => 'monthly',
        'next_maintenance_date' => Carbon::today(),
    ]);

    $report = MaintenanceReport::factory()->draft()->create([
        'website_id' => $website->id,
    ]);

    $this->post(route('maintenance.complete', $report));

    $website->refresh();

    expect($website->next_maintenance_date->format('Y-m-d'))
        ->toBe(Carbon::today()->addMonth()->format('Y-m-d'));
});

test('next maintenance date is calculated correctly for quarterly package', function () {
    $website = Website::factory()->create([
        'maintenance_package' => 'quarterly',
        'next_maintenance_date' => Carbon::today(),
    ]);

    $report = MaintenanceReport::factory()->draft()->create([
        'website_id' => $website->id,
    ]);

    $this->post(route('maintenance.complete', $report));

    $website->refresh();

    expect($website->next_maintenance_date->format('Y-m-d'))
        ->toBe(Carbon::today()->addMonths(3)->format('Y-m-d'));
});

test('next maintenance date is calculated correctly for yearly package', function () {
    $website = Website::factory()->create([
        'maintenance_package' => 'yearly',
        'next_maintenance_date' => Carbon::today(),
    ]);

    $report = MaintenanceReport::factory()->draft()->create([
        'website_id' => $website->id,
    ]);

    $this->post(route('maintenance.complete', $report));

    $website->refresh();

    expect($website->next_maintenance_date->format('Y-m-d'))
        ->toBe(Carbon::today()->addYear()->format('Y-m-d'));
});

test('next maintenance date is null for one_time package', function () {
    $website = Website::factory()->create([
        'maintenance_package' => 'one_time',
        'next_maintenance_date' => Carbon::today(),
    ]);

    $report = MaintenanceReport::factory()->draft()->create([
        'website_id' => $website->id,
    ]);

    $this->post(route('maintenance.complete', $report));

    $website->refresh();

    expect($website->next_maintenance_date)->toBeNull();
});

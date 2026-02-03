<?php

use App\Models\User;
use App\Models\Website;
use App\Models\MaintenanceReport;
use App\Models\PluginUpdate;
use Carbon\Carbon;

test('maintenance report can be created', function () {
    $user = User::factory()->create();
    $website = Website::factory()->create();

    $report = MaintenanceReport::factory()->create([
        'user_id' => $user->id,
        'website_id' => $website->id,
        'status' => 'draft',
    ]);

    expect($report->user_id)->toBe($user->id)
        ->and($report->website_id)->toBe($website->id)
        ->and($report->status)->toBe('draft');
});

test('maintenance report belongs to user', function () {
    $user = User::factory()->create(['name' => 'Test Technician']);
    $report = MaintenanceReport::factory()->create(['user_id' => $user->id]);

    expect($report->user)->toBeInstanceOf(User::class)
        ->and($report->user->name)->toBe('Test Technician');
});

test('maintenance report belongs to website', function () {
    $website = Website::factory()->create(['name' => 'Test Website']);
    $report = MaintenanceReport::factory()->create(['website_id' => $website->id]);

    expect($report->website)->toBeInstanceOf(Website::class)
        ->and($report->website->name)->toBe('Test Website');
});

test('maintenance report has plugin updates relationship', function () {
    $report = MaintenanceReport::factory()->create();
    PluginUpdate::factory()->count(3)->create([
        'maintenance_report_id' => $report->id,
    ]);

    expect($report->pluginUpdates)->toHaveCount(3)
        ->and($report->pluginUpdates->first())->toBeInstanceOf(PluginUpdate::class);
});

test('maintenance report boolean fields are cast correctly', function () {
    $report = MaintenanceReport::factory()->create([
        'backup_completed' => true,
        'php_compatible' => false,
        'check_frontend' => true,
        'check_navigation' => false,
    ]);

    expect($report->backup_completed)->toBeTrue()
        ->and($report->php_compatible)->toBeFalse()
        ->and($report->check_frontend)->toBeTrue()
        ->and($report->check_navigation)->toBeFalse();
});

test('maintenance report dates are cast to carbon', function () {
    $maintenanceDate = Carbon::now();
    $backupDate = Carbon::now()->subHours(2);
    $nextMaintenanceDate = Carbon::now()->addMonth();

    $report = MaintenanceReport::factory()->create([
        'maintenance_date' => $maintenanceDate,
        'backup_datetime' => $backupDate,
        'next_maintenance_date' => $nextMaintenanceDate,
    ]);

    expect($report->maintenance_date)->toBeInstanceOf(Carbon::class)
        ->and($report->backup_datetime)->toBeInstanceOf(Carbon::class)
        ->and($report->next_maintenance_date)->toBeInstanceOf(Carbon::class);
});

test('maintenance report status enum values', function () {
    $report = MaintenanceReport::factory()->create(['status' => 'draft']);
    expect($report->status)->toBe('draft');

    $report->update(['status' => 'completed']);
    expect($report->status)->toBe('completed');

    $report->update(['status' => 'sent']);
    expect($report->status)->toBe('sent');
});

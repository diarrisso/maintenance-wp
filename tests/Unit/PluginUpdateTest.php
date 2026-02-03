<?php

use App\Models\MaintenanceReport;
use App\Models\PluginUpdate;

test('plugin update can be created', function () {
    $report = MaintenanceReport::factory()->create();

    $plugin = PluginUpdate::create([
        'maintenance_report_id' => $report->id,
        'plugin_name' => 'WooCommerce',
        'version_before' => '8.0.0',
        'version_after' => '8.1.0',
    ]);

    expect($plugin->plugin_name)->toBe('WooCommerce')
        ->and($plugin->version_before)->toBe('8.0.0')
        ->and($plugin->version_after)->toBe('8.1.0')
        ->and($plugin->maintenance_report_id)->toBe($report->id);
});

test('plugin update belongs to maintenance report', function () {
    $report = MaintenanceReport::factory()->create();
    $plugin = PluginUpdate::factory()->create([
        'maintenance_report_id' => $report->id,
    ]);

    expect($plugin->maintenanceReport)->toBeInstanceOf(MaintenanceReport::class)
        ->and($plugin->maintenanceReport->id)->toBe($report->id);
});

test('multiple plugin updates can belong to same report', function () {
    $report = MaintenanceReport::factory()->create();

    PluginUpdate::create([
        'maintenance_report_id' => $report->id,
        'plugin_name' => 'Plugin A',
        'version_before' => '1.0.0',
        'version_after' => '1.1.0',
    ]);

    PluginUpdate::create([
        'maintenance_report_id' => $report->id,
        'plugin_name' => 'Plugin B',
        'version_before' => '2.0.0',
        'version_after' => '2.1.0',
    ]);

    expect($report->pluginUpdates)->toHaveCount(2);
});

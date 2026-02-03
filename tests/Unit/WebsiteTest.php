<?php

use App\Models\Client;
use App\Models\Website;
use App\Models\MaintenanceReport;
use Carbon\Carbon;

test('website can be created', function () {
    $client = Client::factory()->create();

    $website = Website::factory()->create([
        'client_id' => $client->id,
        'name' => 'Test Website',
        'url' => 'https://test.com',
    ]);

    expect($website->name)->toBe('Test Website')
        ->and($website->url)->toBe('https://test.com')
        ->and($website->client_id)->toBe($client->id);
});

test('website belongs to client', function () {
    $client = Client::factory()->create(['name' => 'Test Client']);
    $website = Website::factory()->create(['client_id' => $client->id]);

    expect($website->client)->toBeInstanceOf(Client::class)
        ->and($website->client->name)->toBe('Test Client');
});

test('website has maintenance reports relationship', function () {
    $website = Website::factory()->create();
    MaintenanceReport::factory()->count(2)->create(['website_id' => $website->id]);

    expect($website->maintenanceReports)->toHaveCount(2)
        ->and($website->maintenanceReports->first())->toBeInstanceOf(MaintenanceReport::class);
});

test('website maintenance package enum values', function () {
    $website = Website::factory()->create(['maintenance_package' => 'monthly']);
    expect($website->maintenance_package)->toBe('monthly');

    $website->update(['maintenance_package' => 'quarterly']);
    expect($website->maintenance_package)->toBe('quarterly');

    $website->update(['maintenance_package' => 'yearly']);
    expect($website->maintenance_package)->toBe('yearly');

    $website->update(['maintenance_package' => 'one_time']);
    expect($website->maintenance_package)->toBe('one_time');
});

test('website next maintenance date is cast to carbon', function () {
    $date = Carbon::now()->addDays(30);
    $website = Website::factory()->create([
        'next_maintenance_date' => $date,
    ]);

    expect($website->next_maintenance_date)->toBeInstanceOf(Carbon::class)
        ->and($website->next_maintenance_date->format('Y-m-d'))->toBe($date->format('Y-m-d'));
});

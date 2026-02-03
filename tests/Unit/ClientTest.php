<?php

use App\Models\Client;
use App\Models\Website;

test('client can be created', function () {
    $client = Client::factory()->create([
        'name' => 'Test Client',
        'email' => 'test@example.com',
    ]);

    expect($client->name)->toBe('Test Client')
        ->and($client->email)->toBe('test@example.com');
});

test('client has websites relationship', function () {
    $client = Client::factory()->create();
    Website::factory()->count(3)->create(['client_id' => $client->id]);

    expect($client->websites)->toHaveCount(3)
        ->and($client->websites->first())->toBeInstanceOf(Website::class);
});

test('client uses soft deletes', function () {
    $client = Client::factory()->create();
    $clientId = $client->id;

    $client->delete();

    expect(Client::find($clientId))->toBeNull()
        ->and(Client::withTrashed()->find($clientId))->not->toBeNull();
});

test('client fillable attributes work correctly', function () {
    $data = [
        'name' => 'Müller GmbH',
        'email' => 'info@mueller.de',
        'phone' => '+49 30 12345678',
        'company' => 'Müller GmbH',
        'notes' => 'Test notes',
    ];

    $client = Client::create($data);

    expect($client->name)->toBe($data['name'])
        ->and($client->email)->toBe($data['email'])
        ->and($client->phone)->toBe($data['phone'])
        ->and($client->company)->toBe($data['company'])
        ->and($client->notes)->toBe($data['notes']);
});

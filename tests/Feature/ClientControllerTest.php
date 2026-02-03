<?php

use App\Models\User;
use App\Models\Client;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view clients index page', function () {
    Client::factory()->count(3)->create();

    $response = $this->get(route('clients.index'));

    $response->assertStatus(200)
        ->assertViewIs('clients.index')
        ->assertViewHas('clients');
});

test('can search clients by name', function () {
    Client::factory()->create(['name' => 'Müller GmbH']);
    Client::factory()->create(['name' => 'Schmidt & Partner']);

    $response = $this->get(route('clients.index', ['search' => 'Müller']));

    $response->assertStatus(200)
        ->assertSee('Müller GmbH')
        ->assertDontSee('Schmidt & Partner');
});

test('can view create client page', function () {
    $response = $this->get(route('clients.create'));

    $response->assertStatus(200)
        ->assertViewIs('clients.create');
});

test('can store a new client', function () {
    $clientData = [
        'name' => 'Test Client',
        'email' => 'test@example.com',
        'phone' => '+49 30 12345678',
        'company' => 'Test Company',
        'notes' => 'Test notes',
    ];

    $response = $this->post(route('clients.store'), $clientData);

    $response->assertRedirect(route('clients.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('clients', [
        'name' => 'Test Client',
        'email' => 'test@example.com',
    ]);
});

test('client store validation fails with invalid data', function () {
    $response = $this->post(route('clients.store'), [
        'name' => '',
        'email' => 'invalid-email',
    ]);

    $response->assertSessionHasErrors(['name', 'email']);
});

test('can view show client page', function () {
    $client = Client::factory()->create(['name' => 'Test Client']);

    $response = $this->get(route('clients.show', $client));

    $response->assertStatus(200)
        ->assertViewIs('clients.show')
        ->assertViewHas('client')
        ->assertSee('Test Client');
});

test('can view edit client page', function () {
    $client = Client::factory()->create();

    $response = $this->get(route('clients.edit', $client));

    $response->assertStatus(200)
        ->assertViewIs('clients.edit')
        ->assertViewHas('client');
});

test('can update a client', function () {
    $client = Client::factory()->create(['name' => 'Old Name']);

    $response = $this->put(route('clients.update', $client), [
        'name' => 'New Name',
        'email' => $client->email,
    ]);

    $response->assertRedirect(route('clients.show', $client))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'New Name',
    ]);
});

test('can delete a client', function () {
    $client = Client::factory()->create();

    $response = $this->delete(route('clients.destroy', $client));

    $response->assertRedirect(route('clients.index'))
        ->assertSessionHas('success');

    $this->assertSoftDeleted('clients', ['id' => $client->id]);
});

test('guest cannot access client routes', function () {
    Auth::logout();

    $client = Client::factory()->create();

    $this->get(route('clients.index'))->assertRedirect(route('login'));
    $this->get(route('clients.create'))->assertRedirect(route('login'));
    $this->get(route('clients.show', $client))->assertRedirect(route('login'));
    $this->get(route('clients.edit', $client))->assertRedirect(route('login'));
});

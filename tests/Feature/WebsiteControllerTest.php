<?php

use App\Models\User;
use App\Models\Client;
use App\Models\Website;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view websites index page', function () {
    Website::factory()->count(3)->create();

    $response = $this->get(route('websites.index'));

    $response->assertStatus(200)
        ->assertViewIs('websites.index')
        ->assertViewHas('websites');
});

test('can search websites by name', function () {
    Website::factory()->create(['name' => 'Main Website']);
    Website::factory()->create(['name' => 'Shop Website']);

    $response = $this->get(route('websites.index', ['search' => 'Main']));

    $response->assertStatus(200)
        ->assertSee('Main Website')
        ->assertDontSee('Shop Website');
});

test('can view create website page', function () {
    $response = $this->get(route('websites.create'));

    $response->assertStatus(200)
        ->assertViewIs('websites.create')
        ->assertViewHas('clients');
});

test('can store a new website', function () {
    $client = Client::factory()->create();

    $websiteData = [
        'client_id' => $client->id,
        'name' => 'Test Website',
        'url' => 'https://test.com',
        'hosting_provider' => 'Hetzner',
        'php_version' => '8.2',
        'wordpress_version' => '6.4.2',
        'admin_url' => 'https://test.com/wp-admin',
        'maintenance_package' => 'monthly',
        'next_maintenance_date' => Carbon::now()->addMonth()->format('Y-m-d'),
    ];

    $response = $this->post(route('websites.store'), $websiteData);

    $response->assertRedirect(route('websites.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('websites', [
        'name' => 'Test Website',
        'url' => 'https://test.com',
    ]);
});

test('website store validation fails with invalid data', function () {
    $response = $this->post(route('websites.store'), [
        'name' => '',
        'url' => 'invalid-url',
        'client_id' => 99999,
    ]);

    $response->assertSessionHasErrors(['name', 'url', 'client_id']);
});

test('can view show website page', function () {
    $website = Website::factory()->create(['name' => 'Test Website']);

    $response = $this->get(route('websites.show', $website));

    $response->assertStatus(200)
        ->assertViewIs('websites.show')
        ->assertViewHas('website')
        ->assertSee('Test Website');
});

test('can view edit website page', function () {
    $website = Website::factory()->create();

    $response = $this->get(route('websites.edit', $website));

    $response->assertStatus(200)
        ->assertViewIs('websites.edit')
        ->assertViewHas('website')
        ->assertViewHas('clients');
});

test('can update a website', function () {
    $website = Website::factory()->create(['name' => 'Old Name']);
    $client = $website->client;

    $response = $this->put(route('websites.update', $website), [
        'client_id' => $client->id,
        'name' => 'New Name',
        'url' => $website->url,
        'maintenance_package' => 'quarterly',
    ]);

    $response->assertRedirect(route('websites.show', $website))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('websites', [
        'id' => $website->id,
        'name' => 'New Name',
        'maintenance_package' => 'quarterly',
    ]);
});

test('can delete a website', function () {
    $website = Website::factory()->create();

    $response = $this->delete(route('websites.destroy', $website));

    $response->assertRedirect(route('websites.index'))
        ->assertSessionHas('success');

    $this->assertSoftDeleted('websites', ['id' => $website->id]);
});

test('guest cannot access website routes', function () {
    Auth::logout();

    $website = Website::factory()->create();

    $this->get(route('websites.index'))->assertRedirect(route('login'));
    $this->get(route('websites.create'))->assertRedirect(route('login'));
    $this->get(route('websites.show', $website))->assertRedirect(route('login'));
    $this->get(route('websites.edit', $website))->assertRedirect(route('login'));
});

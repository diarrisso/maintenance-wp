<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(
    Tests\TestCase::class,
    RefreshDatabase::class,
)->in('Feature');

uses(
    Tests\TestCase::class,
)->in('Unit');

// Helper functions for tests
function actingAsUser()
{
    return test()->actingAs(\App\Models\User::factory()->create());
}

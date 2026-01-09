<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;

uses(RefreshDatabase::class);

it('403s when user is not a member of the company (fleet)', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Acme Fleet', 'slug' => 'acme-fleet', 'type' => 'fleet']);

    $this->actingAs($user);

    $this->get('/fleet?company=acme-fleet')
        ->assertForbidden();
});

it('loads fleet index when user is a member', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Beta Fleet', 'slug' => 'beta-fleet', 'type' => 'fleet']);

    $user->companies()->attach($company->id);

    $this->actingAs($user);

    $this->get('/fleet?company=beta-fleet')
        ->assertOk()
        ->assertSee('Fleet');
});

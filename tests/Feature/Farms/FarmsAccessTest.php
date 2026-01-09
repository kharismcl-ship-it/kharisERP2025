<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;

uses(RefreshDatabase::class);

it('403s when user is not a member of the company (farms)', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Acme Farms', 'slug' => 'acme', 'type' => 'farm']);

    $this->actingAs($user);

    $this->get('/farms?company=acme')
        ->assertForbidden();
});

it('loads farms index when user is a member', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Beta Farms', 'slug' => 'beta', 'type' => 'farm']);

    $user->companies()->attach($company->id);

    $this->actingAs($user);

    $this->get('/farms?company=beta')
        ->assertOk()
        ->assertSee('Farms');
});

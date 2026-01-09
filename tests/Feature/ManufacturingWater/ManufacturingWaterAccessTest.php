<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;

uses(RefreshDatabase::class);

it('403s when user is not a member of the company (manufacturing water)', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Acme Water', 'slug' => 'acme-water', 'type' => 'water']);

    $this->actingAs($user);

    $this->get('/manufacturing-water?company=acme-water')
        ->assertForbidden();
});

it('loads manufacturing water index when user is a member', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Beta Water', 'slug' => 'beta-water', 'type' => 'water']);

    $user->companies()->attach($company->id);

    $this->actingAs($user);

    $this->get('/manufacturing-water?company=beta-water')
        ->assertOk()
        ->assertSee('Manufacturing — Water');
});

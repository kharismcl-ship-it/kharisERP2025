<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;

uses(RefreshDatabase::class);

it('403s when user is not a member of the company (hr)', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Acme HR', 'slug' => 'acme-hr', 'type' => 'hr']);

    $this->actingAs($user);

    $this->get('/hr?company=acme-hr')
        ->assertForbidden();
});

it('loads hr index when user is a member', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Beta HR', 'slug' => 'beta-hr', 'type' => 'hr']);

    $user->companies()->attach($company->id);

    $this->actingAs($user);

    $this->get('/hr?company=beta-hr')
        ->assertOk()
        ->assertSee('HR');
});

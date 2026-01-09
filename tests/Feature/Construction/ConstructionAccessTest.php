<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;

uses(RefreshDatabase::class);

it('403s when user is not a member of the company (construction)', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme', 'type' => 'construction']);

    $this->actingAs($user);

    $this->get('/construction?company=acme')
        ->assertForbidden();
});

it('loads construction index when user is a member', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Beta LLC', 'slug' => 'beta', 'type' => 'construction']);

    $user->companies()->attach($company->id);

    $this->actingAs($user);

    $this->get('/construction?company=beta')
        ->assertOk()
        ->assertSee('Construction');
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;

uses(RefreshDatabase::class);

it('403s when user is not a member of the company (finance)', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Acme Finance', 'slug' => 'acme-finance', 'type' => 'finance']);

    $this->actingAs($user);

    $this->get('/finance?company=acme-finance')
        ->assertForbidden();
});

it('loads finance index when user is a member', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Beta Finance', 'slug' => 'beta-finance', 'type' => 'finance']);

    $user->companies()->attach($company->id);

    $this->actingAs($user);

    $this->get('/finance?company=beta-finance')
        ->assertOk()
        ->assertSee('Finance');
});

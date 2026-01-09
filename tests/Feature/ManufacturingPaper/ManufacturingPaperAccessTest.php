<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;

uses(RefreshDatabase::class);

it('403s when user is not a member of the company (manufacturing paper)', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Acme Paper', 'slug' => 'acme-paper', 'type' => 'paper']);

    $this->actingAs($user);

    $this->get('/manufacturing-paper?company=acme-paper')
        ->assertForbidden();
});

it('loads manufacturing paper index when user is a member', function () {
    $user = User::factory()->create();
    $company = Company::create(['name' => 'Beta Paper', 'slug' => 'beta-paper', 'type' => 'paper']);

    $user->companies()->attach($company->id);

    $this->actingAs($user);

    $this->get('/manufacturing-paper?company=beta-paper')
        ->assertOk()
        ->assertSee('Manufacturing — Paper');
});

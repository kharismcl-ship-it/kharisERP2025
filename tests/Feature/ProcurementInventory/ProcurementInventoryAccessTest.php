<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('403s when user is not a member of the company (procurement-inventory)', function () {
    $user = User::factory()->create();
    $company = \Modules\Core\Models\Company::create(['name' => 'Acme Corp', 'slug' => 'acme', 'type' => 'procurement']);

    $this->actingAs($user);

    $this->get('/procurement-inventory?company=acme')
        ->assertForbidden();
});

it('loads procurement & inventory index when user is a member', function () {
    $user = User::factory()->create();
    $company = \Modules\Core\Models\Company::create(['name' => 'Beta LLC', 'slug' => 'beta', 'type' => 'procurement']);

    $user->companies()->attach($company->id);

    $this->actingAs($user);

    $this->get('/procurement-inventory?company=beta')
        ->assertOk()
        ->assertSeeText('Procurement');
});

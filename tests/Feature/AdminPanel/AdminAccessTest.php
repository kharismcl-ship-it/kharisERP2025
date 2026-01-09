<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests to login when visiting /admin', function () {
    $this->get('/admin')->assertRedirect();
});

it('allows authenticated users to access the Filament admin panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get('/admin')
        ->assertOk();
});

<?php

use App\Models\Company;
use App\Models\User;

it('guests are redirected from admin HR resources', function () {
    $response = $this->get('/admin/employees');
    $response->assertRedirect();
});

it('authenticated users without company membership cannot see employees', function () {
    $user    = User::factory()->create();
    $company = Company::factory()->create();

    $this->actingAs($user);

    // HR module resources require company context and auth
    $response = $this->get('/admin/employees');
    expect($response->status())->toBeIn([302, 403]);
});

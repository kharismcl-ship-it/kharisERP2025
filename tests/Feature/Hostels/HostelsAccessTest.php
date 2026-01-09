<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Company;
use Modules\Hostels\Models\Hostel;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create([
        'name' => 'Kharis Hostels',
        'slug' => 'hostels',
        'type' => 'hostel',
    ]);
    $this->user = User::factory()->create();
    $this->user->companies()->attach($this->company->id);
    $this->user->update(['current_company_id' => $this->company->id]);

    $this->hostel = Hostel::factory()->create([
        'company_id' => $this->company->id,
    ]);
});

it('loads the hostels list page successfully', function () {
    actingAs($this->user)
        ->get(route('hostels.index'))
        ->assertOk();
});

it('loads the hostel dashboard page successfully', function () {
    actingAs($this->user)
        ->get(route('hostels.dashboard', $this->hostel))
        ->assertOk();
});

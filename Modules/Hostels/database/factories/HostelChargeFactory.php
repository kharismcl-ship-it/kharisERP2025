<?php

namespace Modules\Hostels\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\App\Models\Hostel;
use Modules\Hostels\App\Models\HostelCharge;

class HostelChargeFactory extends Factory
{
    protected $model = HostelCharge::class;

    public function definition(): array
    {
        return [
            'hostel_id' => Hostel::factory(),
            'charge_type' => $this->faker->randomElement(['rent', 'security_deposit', 'late_fee', 'damages']), // Example charge types
            'amount' => $this->faker->numberBetween(1000, 5000),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}

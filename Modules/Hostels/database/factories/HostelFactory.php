<?php

namespace Modules\Hostels\Database\Factories;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Hostel;

class HostelFactory extends Factory
{
    protected $model = Hostel::class;

    public function definition(): array
    {
        return [
            'name' => 'Hostel '.$this->faker->firstName,
            'slug' => $this->faker->slug,
            'code' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'location' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'region' => $this->faker->state,
            'country' => $this->faker->country,
            'capacity' => $this->faker->numberBetween(20, 200),
            'gender_policy' => $this->faker->randomElement(['male', 'female', 'mixed']),
            'check_in_time_default' => '14:00:00',
            'check_out_time_default' => '12:00:00',
            'status' => 'active',
            'require_deposit' => true,
            'deposit_amount' => $this->faker->randomFloat(2, 100, 500),
            'deposit_percentage' => $this->faker->randomFloat(2, 5, 20),
            'deposit_type' => $this->faker->randomElement(['security', 'utility', 'damage', 'percentage']),
            'deposit_refund_policy' => $this->faker->sentence,
            'company_id' => CompanyFactory::new()->create(['type' => 'hostel'])->id,
        ];
    }
}

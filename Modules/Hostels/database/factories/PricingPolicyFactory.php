<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\PricingPolicy;

class PricingPolicyFactory extends Factory
{
    protected $model = PricingPolicy::class;

    public function definition(): array
    {
        return [
            'hostel_id' => function () {
                return \Modules\Hostels\Models\Hostel::factory()->create()->id;
            },
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'policy_type' => $this->faker->randomElement(['seasonal', 'demand', 'length_of_stay', 'special_event']),
            'adjustment_type' => $this->faker->randomElement(['percentage', 'fixed_amount']),
            'adjustment_value' => $this->faker->randomFloat(2, 5, 50),
            'is_active' => true,
            'conditions' => null,
            'valid_from' => null,
            'valid_to' => null,
        ];
    }

    public function seasonal(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'policy_type' => 'seasonal',
                'conditions' => [
                    ['type' => 'month', 'values' => [12, 1, 2]], // December-February
                ],
            ];
        });
    }

    public function lengthOfStay(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'policy_type' => 'length_of_stay',
                'conditions' => [
                    ['type' => 'min_nights', 'value' => 3],
                ],
            ];
        });
    }

    public function demandBased(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'policy_type' => 'demand',
                'conditions' => [
                    ['type' => 'advance_booking', 'value' => 7], // Within 7 days
                ],
            ];
        });
    }
}

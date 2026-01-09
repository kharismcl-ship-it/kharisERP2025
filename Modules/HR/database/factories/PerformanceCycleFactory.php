<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\PerformanceCycle;

class PerformanceCycleFactory extends Factory
{
    protected $model = PerformanceCycle::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->sentence(3),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['planned', 'open', 'closed']),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}

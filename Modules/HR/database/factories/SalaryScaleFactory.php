<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\SalaryScale;

class SalaryScaleFactory extends Factory
{
    protected $model = SalaryScale::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->sentence(2),
            'code' => $this->faker->optional()->lexify('???-####'),
            'min_basic' => $this->faker->randomFloat(2, 1000, 5000),
            'max_basic' => $this->faker->randomFloat(2, 5000, 15000),
            'currency' => 'GHS',
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}

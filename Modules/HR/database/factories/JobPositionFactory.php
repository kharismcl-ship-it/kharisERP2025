<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\JobPosition;

class JobPositionFactory extends Factory
{
    protected $model = JobPosition::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->jobTitle,
            'description' => $this->faker->sentence,
        ];
    }
}

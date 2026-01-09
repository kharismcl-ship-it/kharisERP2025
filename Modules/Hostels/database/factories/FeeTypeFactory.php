<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Models\Company;
use Modules\Hostels\Models\FeeType;

class FeeTypeFactory extends Factory
{
    protected $model = FeeType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'amount' => $this->faker->randomFloat(2, 10, 100),
            'company_id' => Company::factory(),
        ];
    }
}

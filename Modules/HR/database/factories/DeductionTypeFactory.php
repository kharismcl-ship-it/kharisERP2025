<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\DeductionType;

class DeductionTypeFactory extends Factory
{
    protected $model = DeductionType::class;

    public function definition(): array
    {
        return [
            'company_id'       => Company::factory(),
            'name'             => $this->faker->words(2, true) . ' Deduction',
            'code'             => strtoupper($this->faker->unique()->lexify('??')),
            'category'         => $this->faker->randomElement(['tax', 'social_security', 'pension', 'loan', 'other']),
            'calculation_type' => 'fixed',
            'default_amount'   => 50.00,
            'percentage_value' => null,
            'is_active'        => true,
        ];
    }
}

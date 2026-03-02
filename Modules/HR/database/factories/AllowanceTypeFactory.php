<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\AllowanceType;

class AllowanceTypeFactory extends Factory
{
    protected $model = AllowanceType::class;

    public function definition(): array
    {
        return [
            'company_id'        => Company::factory(),
            'name'              => $this->faker->words(2, true) . ' Allowance',
            'code'              => strtoupper($this->faker->unique()->lexify('??')),
            'calculation_type'  => 'fixed',
            'default_amount'    => 200.00,
            'percentage_value'  => null,
            'is_taxable'        => true,
            'is_pensionable'    => false,
            'is_active'         => true,
        ];
    }

    public function percentage(float $pct = 10.0): static
    {
        return $this->state(['calculation_type' => 'percentage', 'percentage_value' => $pct, 'default_amount' => null]);
    }
}

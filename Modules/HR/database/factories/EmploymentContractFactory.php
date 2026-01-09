<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmploymentContract;

class EmploymentContractFactory extends Factory
{
    protected $model = EmploymentContract::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'contract_number' => $this->faker->unique()->randomNumber(5),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->optional()->date(),
            'contract_type' => $this->faker->randomElement(['permanent', 'fixed_term', 'casual']),
            'probation_end_date' => $this->faker->optional()->date(),
            'is_current' => $this->faker->boolean,
            'basic_salary' => $this->faker->randomFloat(2, 1000, 10000),
            'currency' => 'GHS',
            'working_hours_per_week' => $this->faker->randomFloat(2, 20, 60),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}

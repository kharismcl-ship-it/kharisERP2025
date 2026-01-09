<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeSalary;
use Modules\HR\Models\SalaryScale;

class EmployeeSalaryFactory extends Factory
{
    protected $model = EmployeeSalary::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'salary_scale_id' => SalaryScale::factory(),
            'basic_salary' => $this->faker->randomFloat(2, 1000, 10000),
            'currency' => 'GHS',
            'effective_from' => $this->faker->date(),
            'effective_to' => $this->faker->optional()->date(),
            'is_current' => $this->faker->boolean,
        ];
    }
}

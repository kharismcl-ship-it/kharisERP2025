<?php

namespace Modules\HR\Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeCompanyAssignment;

class EmployeeCompanyAssignmentFactory extends Factory
{
    protected $model = EmployeeCompanyAssignment::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'company_id' => Company::factory(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->optional()->date(),
            'assignment_reason' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }
}

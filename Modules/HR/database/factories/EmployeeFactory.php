<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Employee;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;

        return [
            'company_id' => Company::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'employee_code' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{4}'),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'hire_date' => $this->faker->dateTimeThisDecade,
            'employment_type' => $this->faker->randomElement(['full_time', 'part_time', 'contract']),
            'employment_status' => $this->faker->randomElement(['active', 'inactive', 'terminated']),
        ];
    }
}

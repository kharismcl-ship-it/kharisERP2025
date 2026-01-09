<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Hostel;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HostelStaffAssignment;

class HostelStaffAssignmentFactory extends Factory
{
    protected $model = HostelStaffAssignment::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'hostel_id' => Hostel::factory(),
            'role' => $this->faker->randomElement(['Manager', 'Receptionist', 'Cleaner', 'Security', 'Maintenance']),
            'assigned_at' => $this->faker->dateTimeThisYear,
        ];
    }
}

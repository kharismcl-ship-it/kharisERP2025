<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\AttendanceRecord;
use Modules\HR\Models\Employee;

class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['present', 'absent', 'leave', 'off']),
            'check_in_time' => $this->faker->optional()->dateTime(),
            'check_out_time' => $this->faker->optional()->dateTime(),
        ];
    }
}

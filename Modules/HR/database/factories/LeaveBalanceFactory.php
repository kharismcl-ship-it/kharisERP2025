<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveType;

class LeaveBalanceFactory extends Factory
{
    protected $model = LeaveBalance::class;

    public function definition(): array
    {
        return [
            'company_id'      => Company::factory(),
            'employee_id'     => Employee::factory(),
            'leave_type_id'   => LeaveType::factory(),
            'year'            => now()->year,
            'initial_balance' => 20.0,
            'current_balance' => 20.0,
            'used_balance'    => 0.0,
            'carried_over'    => 0.0,
            'adjustments'     => 0.0,
        ];
    }
}

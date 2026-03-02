<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\LeaveType;

class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        return [
            'company_id'        => Company::factory(),
            'name'              => $this->faker->randomElement(['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave', 'Unpaid Leave']),
            'max_days_per_year' => $this->faker->numberBetween(5, 30),
            'is_active'         => true,
            'requires_approval' => true,
            'is_paid'           => true,
            'has_accrual'       => false,
            'accrual_rate'      => 1.67,
            'accrual_frequency' => 'monthly',
            'carryover_limit'   => 0.0,
            'max_balance'       => null,
            'pro_rata_enabled'  => false,
        ];
    }
}

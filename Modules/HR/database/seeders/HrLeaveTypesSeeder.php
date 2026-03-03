<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrLeaveTypesSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrLeaveTypesSeeder.');
            return;
        }

        $leaveTypes = [
            [
                'name'              => 'Annual Leave',
                'code'              => 'AL',
                'description'       => 'Standard paid annual leave entitlement for all employees.',
                'max_days_per_year' => 20,
                'requires_approval' => true,
                'is_paid'           => true,
                'has_accrual'       => true,
                'accrual_rate'      => 1.67,
                'accrual_frequency' => 'monthly',
                'carryover_limit'   => 5,
                'max_balance'       => 40,
                'pro_rata_enabled'  => true,
            ],
            [
                'name'              => 'Sick Leave',
                'code'              => 'SL',
                'description'       => 'Paid leave for illness or medical appointments.',
                'max_days_per_year' => 10,
                'requires_approval' => true,
                'is_paid'           => true,
                'has_accrual'       => false,
                'accrual_rate'      => 0,
                'accrual_frequency' => 'monthly',
                'carryover_limit'   => 0,
                'max_balance'       => null,
                'pro_rata_enabled'  => false,
            ],
            [
                'name'              => 'Maternity Leave',
                'code'              => 'ML',
                'description'       => 'Paid maternity leave for female employees following childbirth.',
                'max_days_per_year' => 90,
                'requires_approval' => false,
                'is_paid'           => true,
                'has_accrual'       => false,
                'accrual_rate'      => 0,
                'accrual_frequency' => 'monthly',
                'carryover_limit'   => 0,
                'max_balance'       => null,
                'pro_rata_enabled'  => false,
            ],
            [
                'name'              => 'Study Leave',
                'code'              => 'STL',
                'description'       => 'Leave for approved academic or professional development activities.',
                'max_days_per_year' => 5,
                'requires_approval' => true,
                'is_paid'           => false,
                'has_accrual'       => false,
                'accrual_rate'      => 0,
                'accrual_frequency' => 'monthly',
                'carryover_limit'   => 0,
                'max_balance'       => null,
                'pro_rata_enabled'  => false,
            ],
            [
                'name'              => 'Emergency Leave',
                'code'              => 'EL',
                'description'       => 'Short notice leave for urgent personal or family emergencies.',
                'max_days_per_year' => 3,
                'requires_approval' => false,
                'is_paid'           => true,
                'has_accrual'       => false,
                'accrual_rate'      => 0,
                'accrual_frequency' => 'monthly',
                'carryover_limit'   => 0,
                'max_balance'       => null,
                'pro_rata_enabled'  => false,
            ],
        ];

        foreach ($leaveTypes as $lt) {
            DB::table('hr_leave_types')->updateOrInsert(
                ['company_id' => $company->id, 'code' => $lt['code']],
                array_merge($lt, [
                    'company_id' => $company->id,
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('HrLeaveTypesSeeder: '.count($leaveTypes).' leave types seeded.');
    }
}

<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrLeaveBalancesSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrLeaveBalancesSeeder.');
            return;
        }

        $year = now()->year;

        $employees = DB::table('hr_employees')
            ->where('company_id', $company->id)
            ->whereIn('employment_status', ['active', 'probation'])
            ->get();

        $leaveTypes = DB::table('hr_leave_types')
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->get();

        $created = 0;

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $lt) {
                $initialBalance = $lt->max_days_per_year ?? 0;

                // Probation employees get half entitlement
                if ($employee->employment_status === 'probation') {
                    $initialBalance = round($initialBalance / 2, 1);
                }

                $exists = DB::table('hr_leave_balances')
                    ->where('employee_id', $employee->id)
                    ->where('leave_type_id', $lt->id)
                    ->where('year', $year)
                    ->exists();

                if (! $exists) {
                    DB::table('hr_leave_balances')->insert([
                        'company_id'        => $company->id,
                        'employee_id'       => $employee->id,
                        'leave_type_id'     => $lt->id,
                        'year'              => $year,
                        'initial_balance'   => $initialBalance,
                        'used_balance'      => 0,
                        'current_balance'   => $initialBalance,
                        'carried_over'      => 0,
                        'adjustments'       => 0,
                        'last_calculated_at'=> now(),
                        'notes'             => null,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                    $created++;
                }
            }
        }

        $this->command->info("HrLeaveBalancesSeeder: {$created} balance records created for year {$year}.");
    }
}

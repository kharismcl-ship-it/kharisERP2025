<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrEmployeeSalariesSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrEmployeeSalariesSeeder.');
            return;
        }

        $scales = DB::table('hr_salary_scales')
            ->where('company_id', $company->id)
            ->pluck('id', 'code');

        $jnrId = $scales['JNR'] ?? null;
        $midId = $scales['MID'] ?? null;
        $snrId = $scales['SNR'] ?? null;

        // Map employee_code → [salary_scale_code, basic_salary]
        $salaryMap = [
            'EMP001' => [$snrId, 14000.00], // CEO
            'EMP002' => [$snrId, 12000.00], // COO
            'EMP003' => [$snrId,  7500.00], // HR Manager
            'EMP004' => [$snrId, 13000.00], // CFO
            'EMP005' => [$snrId, 12500.00], // CTO
            'EMP006' => [$midId,  5500.00], // Sales Manager
            'EMP007' => [$midId,  5200.00], // Operations Manager
            'EMP008' => [$midId,  3800.00], // HR Officer
            'EMP009' => [$midId,  5000.00], // Finance Manager
            'EMP010' => [$midId,  3200.00], // Accountant
            'EMP011' => [$midId,  4500.00], // Software Engineer
            'EMP012' => [$midId,  4200.00], // Software Engineer
            'EMP013' => [$jnrId,  2500.00], // IT Support
            'EMP014' => [$jnrId,  2800.00], // Sales Rep
            'EMP015' => [$jnrId,  2800.00], // Sales Rep
            'EMP016' => [$midId,  4800.00], // Marketing Manager
            'EMP017' => [$jnrId,  2600.00], // Marketing Officer
            'EMP018' => [$snrId,  7000.00], // Legal Counsel
            'EMP019' => [$jnrId,  2200.00], // Operations Officer (contract)
            'EMP020' => [$jnrId,  1800.00], // Recruitment Specialist (probation)
        ];

        $employees = DB::table('hr_employees')
            ->where('company_id', $company->id)
            ->pluck('id', 'employee_code');

        $created = 0;

        foreach ($salaryMap as $code => [$scaleId, $basicSalary]) {
            $empId = $employees[$code] ?? null;

            if (! $empId) {
                continue;
            }

            $exists = DB::table('hr_employee_salaries')
                ->where('employee_id', $empId)
                ->where('is_current', true)
                ->exists();

            if (! $exists) {
                DB::table('hr_employee_salaries')->insert([
                    'employee_id'    => $empId,
                    'company_id'     => $company->id,
                    'salary_scale_id'=> $scaleId,
                    'basic_salary'   => $basicSalary,
                    'currency'       => 'GHS',
                    'effective_from' => '2026-01-01',
                    'effective_to'   => null,
                    'is_current'     => true,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
                $created++;
            }
        }

        $this->command->info("HrEmployeeSalariesSeeder: {$created} salary records seeded.");
    }
}

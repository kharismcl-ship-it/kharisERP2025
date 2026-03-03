<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrEmployeesSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrEmployeesSeeder.');
            return;
        }

        $depts = DB::table('hr_departments')
            ->where('company_id', $company->id)
            ->pluck('id', 'code');

        $positions = DB::table('hr_job_positions')
            ->where('company_id', $company->id)
            ->pluck('id', 'code');

        // Define employees in order — managers first so we can reference their IDs
        $employeeData = [
            // Executives / Managers (no reporting_to)
            [
                'code' => 'EMP001', 'first' => 'James',   'last' => 'Mensah',
                'gender' => 'male',   'dept' => 'EXEC',  'pos' => 'CEO',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100001', 'email' => 'james.mensah@company.com',
                'hire_date' => '2020-01-15', 'reports_to_code' => null,
            ],
            [
                'code' => 'EMP002', 'first' => 'Abena',   'last' => 'Asante',
                'gender' => 'female', 'dept' => 'EXEC',  'pos' => 'COO',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100002', 'email' => 'abena.asante@company.com',
                'hire_date' => '2020-03-01', 'reports_to_code' => 'EMP001',
            ],
            [
                'code' => 'EMP003', 'first' => 'Kofi',    'last' => 'Osei',
                'gender' => 'male',   'dept' => 'HR',    'pos' => 'HRM',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100003', 'email' => 'kofi.osei@company.com',
                'hire_date' => '2020-04-10', 'reports_to_code' => 'EMP001',
            ],
            [
                'code' => 'EMP004', 'first' => 'Akosua',  'last' => 'Boateng',
                'gender' => 'female', 'dept' => 'FIN',   'pos' => 'CFO',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100004', 'email' => 'akosua.boateng@company.com',
                'hire_date' => '2020-02-01', 'reports_to_code' => 'EMP001',
            ],
            [
                'code' => 'EMP005', 'first' => 'Kwame',   'last' => 'Acheampong',
                'gender' => 'male',   'dept' => 'IT',    'pos' => 'CTO',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100005', 'email' => 'kwame.acheampong@company.com',
                'hire_date' => '2020-06-15', 'reports_to_code' => 'EMP001',
            ],
            [
                'code' => 'EMP006', 'first' => 'Efua',    'last' => 'Darko',
                'gender' => 'female', 'dept' => 'SALES', 'pos' => 'SM',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100006', 'email' => 'efua.darko@company.com',
                'hire_date' => '2021-01-05', 'reports_to_code' => 'EMP002',
            ],
            [
                'code' => 'EMP007', 'first' => 'Yaw',     'last' => 'Ofori',
                'gender' => 'male',   'dept' => 'OPS',   'pos' => 'OM',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100007', 'email' => 'yaw.ofori@company.com',
                'hire_date' => '2021-03-20', 'reports_to_code' => 'EMP002',
            ],

            // Mid-level
            [
                'code' => 'EMP008', 'first' => 'Ama',     'last' => 'Owusu',
                'gender' => 'female', 'dept' => 'HR',    'pos' => 'HRO',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100008', 'email' => 'ama.owusu@company.com',
                'hire_date' => '2021-06-01', 'reports_to_code' => 'EMP003',
            ],
            [
                'code' => 'EMP009', 'first' => 'Nana',    'last' => 'Adjei',
                'gender' => 'male',   'dept' => 'FIN',   'pos' => 'FM',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100009', 'email' => 'nana.adjei@company.com',
                'hire_date' => '2021-07-12', 'reports_to_code' => 'EMP004',
            ],
            [
                'code' => 'EMP010', 'first' => 'Serwa',   'last' => 'Amoah',
                'gender' => 'female', 'dept' => 'FIN',   'pos' => 'ACCT',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100010', 'email' => 'serwa.amoah@company.com',
                'hire_date' => '2021-08-01', 'reports_to_code' => 'EMP009',
            ],
            [
                'code' => 'EMP011', 'first' => 'Kojo',    'last' => 'Amponsah',
                'gender' => 'male',   'dept' => 'IT',    'pos' => 'SE',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100011', 'email' => 'kojo.amponsah@company.com',
                'hire_date' => '2022-01-10', 'reports_to_code' => 'EMP005',
            ],
            [
                'code' => 'EMP012', 'first' => 'Adwoa',   'last' => 'Frimpong',
                'gender' => 'female', 'dept' => 'IT',    'pos' => 'SE',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100012', 'email' => 'adwoa.frimpong@company.com',
                'hire_date' => '2022-02-14', 'reports_to_code' => 'EMP005',
            ],
            [
                'code' => 'EMP013', 'first' => 'Kweku',   'last' => 'Asare',
                'gender' => 'male',   'dept' => 'IT',    'pos' => 'ITSS',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100013', 'email' => 'kweku.asare@company.com',
                'hire_date' => '2022-04-01', 'reports_to_code' => 'EMP005',
            ],
            [
                'code' => 'EMP014', 'first' => 'Akua',    'last' => 'Bonsu',
                'gender' => 'female', 'dept' => 'SALES', 'pos' => 'SR',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100014', 'email' => 'akua.bonsu@company.com',
                'hire_date' => '2022-05-16', 'reports_to_code' => 'EMP006',
            ],
            [
                'code' => 'EMP015', 'first' => 'Fiifi',   'last' => 'Quaye',
                'gender' => 'male',   'dept' => 'SALES', 'pos' => 'SR',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100015', 'email' => 'fiifi.quaye@company.com',
                'hire_date' => '2022-06-01', 'reports_to_code' => 'EMP006',
            ],
            [
                'code' => 'EMP016', 'first' => 'Maame',   'last' => 'Sarpong',
                'gender' => 'female', 'dept' => 'MKT',   'pos' => 'MM',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100016', 'email' => 'maame.sarpong@company.com',
                'hire_date' => '2022-07-01', 'reports_to_code' => 'EMP002',
            ],
            [
                'code' => 'EMP017', 'first' => 'Kwasi',   'last' => 'Asiedu',
                'gender' => 'male',   'dept' => 'MKT',   'pos' => 'MO',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100017', 'email' => 'kwasi.asiedu@company.com',
                'hire_date' => '2022-09-05', 'reports_to_code' => 'EMP016',
            ],
            [
                'code' => 'EMP018', 'first' => 'Esi',     'last' => 'Hammond',
                'gender' => 'female', 'dept' => 'LEGAL', 'pos' => 'LC',
                'type' => 'full_time', 'status' => 'active',
                'phone' => '0244100018', 'email' => 'esi.hammond@company.com',
                'hire_date' => '2021-10-01', 'reports_to_code' => 'EMP001',
            ],
            [
                'code' => 'EMP019', 'first' => 'Mensah',  'last' => 'Tetteh',
                'gender' => 'male',   'dept' => 'OPS',   'pos' => 'OO',
                'type' => 'contract', 'status' => 'active',
                'phone' => '0244100019', 'email' => 'mensah.tetteh@company.com',
                'hire_date' => '2023-01-10', 'reports_to_code' => 'EMP007',
            ],
            [
                'code' => 'EMP020', 'first' => 'Afia',    'last' => 'Dankwa',
                'gender' => 'female', 'dept' => 'HR',    'pos' => 'RS',
                'type' => 'full_time', 'status' => 'probation',
                'phone' => '0244100020', 'email' => 'afia.dankwa@company.com',
                'hire_date' => '2026-01-15', 'reports_to_code' => 'EMP003',
            ],
        ];

        // First pass: insert all employees (without reporting_to to avoid FK issues)
        foreach ($employeeData as $emp) {
            $fullName = $emp['first'].' '.$emp['last'];
            DB::table('hr_employees')->updateOrInsert(
                ['company_id' => $company->id, 'employee_code' => $emp['code']],
                [
                    'company_id'              => $company->id,
                    'employee_code'           => $emp['code'],
                    'first_name'              => $emp['first'],
                    'last_name'               => $emp['last'],
                    'full_name'               => $fullName,
                    'gender'                  => $emp['gender'],
                    'phone'                   => $emp['phone'],
                    'email'                   => $emp['email'],
                    'department_id'           => $depts[$emp['dept']] ?? null,
                    'job_position_id'         => $positions[$emp['pos']] ?? null,
                    'hire_date'               => $emp['hire_date'],
                    'employment_type'         => $emp['type'],
                    'employment_status'       => $emp['status'],
                    'reporting_to_employee_id'=> null,  // set in second pass
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]
            );
        }

        // Build code → id map
        $empIds = DB::table('hr_employees')
            ->where('company_id', $company->id)
            ->pluck('id', 'employee_code');

        // Second pass: set reporting_to_employee_id
        foreach ($employeeData as $emp) {
            if ($emp['reports_to_code']) {
                $managerId = $empIds[$emp['reports_to_code']] ?? null;
                if ($managerId) {
                    DB::table('hr_employees')
                        ->where('company_id', $company->id)
                        ->where('employee_code', $emp['code'])
                        ->update(['reporting_to_employee_id' => $managerId]);
                }
            }
        }

        $this->command->info('HrEmployeesSeeder: '.count($employeeData).' employees seeded.');
    }
}

<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrJobPositionsSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrJobPositionsSeeder.');
            return;
        }

        // Build department lookup
        $depts = DB::table('hr_departments')
            ->where('company_id', $company->id)
            ->pluck('id', 'code');

        $positions = [
            // Executive
            ['title' => 'Chief Executive Officer',     'code' => 'CEO',  'dept' => 'EXEC',  'grade' => 'E1'],
            ['title' => 'Chief Operating Officer',     'code' => 'COO',  'dept' => 'EXEC',  'grade' => 'E1'],

            // HR
            ['title' => 'HR Manager',                  'code' => 'HRM',  'dept' => 'HR',    'grade' => 'M1'],
            ['title' => 'HR Officer',                  'code' => 'HRO',  'dept' => 'HR',    'grade' => 'S2'],
            ['title' => 'Recruitment Specialist',      'code' => 'RS',   'dept' => 'HR',    'grade' => 'S1'],

            // Finance
            ['title' => 'Chief Financial Officer',     'code' => 'CFO',  'dept' => 'FIN',   'grade' => 'E1'],
            ['title' => 'Finance Manager',             'code' => 'FM',   'dept' => 'FIN',   'grade' => 'M1'],
            ['title' => 'Accountant',                  'code' => 'ACCT', 'dept' => 'FIN',   'grade' => 'S1'],

            // IT
            ['title' => 'Chief Technology Officer',    'code' => 'CTO',  'dept' => 'IT',    'grade' => 'E1'],
            ['title' => 'Software Engineer',           'code' => 'SE',   'dept' => 'IT',    'grade' => 'S2'],
            ['title' => 'IT Support Specialist',       'code' => 'ITSS', 'dept' => 'IT',    'grade' => 'S1'],

            // Operations
            ['title' => 'Operations Manager',          'code' => 'OM',   'dept' => 'OPS',   'grade' => 'M1'],
            ['title' => 'Operations Officer',          'code' => 'OO',   'dept' => 'OPS',   'grade' => 'S1'],

            // Sales
            ['title' => 'Sales Manager',               'code' => 'SM',   'dept' => 'SALES', 'grade' => 'M1'],
            ['title' => 'Sales Representative',        'code' => 'SR',   'dept' => 'SALES', 'grade' => 'S1'],

            // Marketing
            ['title' => 'Marketing Manager',           'code' => 'MM',   'dept' => 'MKT',   'grade' => 'M1'],
            ['title' => 'Marketing Officer',           'code' => 'MO',   'dept' => 'MKT',   'grade' => 'S1'],

            // Legal
            ['title' => 'Legal Counsel',               'code' => 'LC',   'dept' => 'LEGAL', 'grade' => 'M1'],
        ];

        foreach ($positions as $pos) {
            $deptId = $depts[$pos['dept']] ?? null;

            DB::table('hr_job_positions')->updateOrInsert(
                ['company_id' => $company->id, 'code' => $pos['code']],
                [
                    'company_id'    => $company->id,
                    'department_id' => $deptId,
                    'title'         => $pos['title'],
                    'code'          => $pos['code'],
                    'grade'         => $pos['grade'],
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
        }

        $this->command->info('HrJobPositionsSeeder: '.count($positions).' positions seeded.');
    }
}

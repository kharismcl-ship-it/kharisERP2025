<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrDepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrDepartmentsSeeder.');
            return;
        }

        $departments = [
            ['name' => 'Executive',             'code' => 'EXEC',  'description' => 'Executive leadership and board oversight'],
            ['name' => 'Human Resources',       'code' => 'HR',    'description' => 'People management, recruitment, and employee relations'],
            ['name' => 'Finance',               'code' => 'FIN',   'description' => 'Financial planning, accounting, and reporting'],
            ['name' => 'Information Technology','code' => 'IT',    'description' => 'Technology infrastructure and software development'],
            ['name' => 'Operations',            'code' => 'OPS',   'description' => 'Day-to-day operational management and logistics'],
            ['name' => 'Sales',                 'code' => 'SALES', 'description' => 'Revenue generation and client acquisition'],
            ['name' => 'Marketing',             'code' => 'MKT',   'description' => 'Brand management and marketing campaigns'],
            ['name' => 'Legal',                 'code' => 'LEGAL', 'description' => 'Legal counsel, compliance, and contract management'],
        ];

        foreach ($departments as $dept) {
            DB::table('hr_departments')->updateOrInsert(
                ['company_id' => $company->id, 'code' => $dept['code']],
                [
                    'company_id'  => $company->id,
                    'name'        => $dept['name'],
                    'code'        => $dept['code'],
                    'description' => $dept['description'],
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }

        $this->command->info('HrDepartmentsSeeder: '.count($departments).' departments seeded.');
    }
}

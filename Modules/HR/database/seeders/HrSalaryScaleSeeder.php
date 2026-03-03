<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrSalaryScaleSeeder extends Seeder
{
    public function run(): void
    {
        $company = DB::table('companies')->first();

        if (! $company) {
            $this->command->warn('No company found. Skipping HrSalaryScaleSeeder.');
            return;
        }

        $scales = [
            [
                'name'        => 'Junior',
                'code'        => 'JNR',
                'min_basic'   => 1500.00,
                'max_basic'   => 3000.00,
                'currency'    => 'GHS',
                'description' => 'Entry-level and junior staff salary band.',
            ],
            [
                'name'        => 'Mid-Level',
                'code'        => 'MID',
                'min_basic'   => 3001.00,
                'max_basic'   => 6000.00,
                'currency'    => 'GHS',
                'description' => 'Mid-level professional and senior officer salary band.',
            ],
            [
                'name'        => 'Senior',
                'code'        => 'SNR',
                'min_basic'   => 6001.00,
                'max_basic'   => 15000.00,
                'currency'    => 'GHS',
                'description' => 'Senior management and executive salary band.',
            ],
        ];

        foreach ($scales as $scale) {
            DB::table('hr_salary_scales')->updateOrInsert(
                ['company_id' => $company->id, 'code' => $scale['code']],
                array_merge($scale, [
                    'company_id' => $company->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('HrSalaryScaleSeeder: '.count($scales).' salary scales seeded.');
    }
}

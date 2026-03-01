<?php

namespace Modules\ManufacturingWater\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class MwCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug'     => 'mw_distribution_completed',
                'name'     => 'Manufacturing Water: Distribution Completed',
                'channel'  => 'email',
                'subject'  => 'Water Distribution Completed — {{distribution_reference}}',
                'body'     => "Dear Team,\n\nWater distribution {{distribution_reference}} has been completed.\n\nDestination: {{destination}}\nDistribution Date: {{distribution_date}}\nVolume: {{volume_liters}} litres\nUnit Price: {{currency}} {{unit_price}}/L\nTotal Amount: {{currency}} {{total_amount}}\n\nAn invoice has been generated for this distribution.\n\nRegards,\nWater Distribution Management",
                'is_active'=> true,
            ],
            [
                'slug'     => 'mw_water_test_failed',
                'name'     => 'Manufacturing Water: Water Test Failed',
                'channel'  => 'email',
                'subject'  => 'Water Quality Test FAILED — Immediate Action Required',
                'body'     => "Dear Lab Team,\n\nA water quality test has FAILED.\n\nTest Date: {{test_date}}\nTest Type: {{test_type}}\nTested By: {{tested_by}}\n\nFailed Parameters:\n- pH: {{ph}}\n- Turbidity (NTU): {{turbidity_ntu}}\n- TDS (PPM): {{tds_ppm}}\n- Coliform Count: {{coliform_count}}\n- Chlorine Residual: {{chlorine_residual}}\n\nNotes: {{notes}}\n\nDistribution must be HALTED immediately until this issue is resolved.\n\nRegards,\nWater Quality Control System",
                'is_active'=> true,
            ],
        ];

        foreach ($templates as $template) {
            CommTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
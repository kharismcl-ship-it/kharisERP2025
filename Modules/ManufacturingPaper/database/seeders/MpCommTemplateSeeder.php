<?php

namespace Modules\ManufacturingPaper\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class MpCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug'     => 'mp_batch_completed',
                'name'     => 'Manufacturing Paper: Batch Completed',
                'channel'  => 'email',
                'subject'  => 'Production Batch Completed — {{batch_number}}',
                'body'     => "Dear Team,\n\nProduction batch {{batch_number}} has been completed.\n\nPaper Grade: {{paper_grade}}\nQuantity Produced: {{quantity_produced}} {{unit}}\nWaste Quantity: {{waste_quantity}} {{unit}}\nEfficiency: {{efficiency_pct}}%\nProduction Cost: {{currency}} {{production_cost}}\nCompleted At: {{end_time}}\n\nThe batch is now ready for quality inspection and dispatch.\n\nRegards,\nManufacturing Team",
                'is_active'=> true,
            ],
            [
                'slug'     => 'mp_quality_failed',
                'name'     => 'Manufacturing Paper: Quality Check Failed',
                'channel'  => 'email',
                'subject'  => 'Quality Failure Alert — Batch {{batch_number}}',
                'body'     => "Dear QC Team,\n\nA quality check has FAILED for the following batch:\n\nBatch: {{batch_number}}\nPaper Grade: {{paper_grade}}\nTest Date: {{test_date}}\nTested By: {{tested_by}}\n\nTest Results:\n- Tensile CD: {{tensile_cd}}\n- Tensile MD: {{tensile_md}}\n- Burst Strength: {{burst_strength}}\n- Moisture: {{moisture_percent}}%\n- Brightness: {{brightness}}\n\nNotes: {{notes}}\n\nImmediate review and corrective action is required.\n\nRegards,\nQuality Control System",
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
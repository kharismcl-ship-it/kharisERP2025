<?php

namespace Modules\Construction\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CommunicationCentre\Models\CommTemplate;

class ConstructionCommTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code'    => 'construction_project_milestone',
                'name'    => 'Construction — Phase / Milestone Completed',
                'channel' => 'email',
                'subject' => 'Project Update: {{phase_name}} Completed — {{project_name}}',
                'body'    => "Dear {{client_name}},\n\nWe are pleased to inform you that the following phase of your project has been completed:\n\nProject: {{project_name}}\nPhase: {{phase_name}}\nOverall Progress: {{progress}}\nContract Value: {{currency}} {{contract_value}}\n\nAn invoice for this milestone has been issued and will arrive separately.\n\nThank you for your continued trust.",
            ],
            [
                'code'    => 'construction_budget_overrun',
                'name'    => 'Construction — Budget Overrun Alert',
                'channel' => 'email',
                'subject' => 'Budget Alert: {{project_name}} has exceeded budget by {{currency}} {{overrun_amount}}',
                'body'    => "Dear {{client_name}},\n\nThis is an important notice regarding your project:\n\nProject: {{project_name}}\nApproved Budget: {{currency}} {{budget}}\nTotal Spent to Date: {{currency}} {{total_spent}}\nOverrun Amount: {{currency}} {{overrun_amount}} ({{overrun_pct}}%)\n\nPlease contact your project manager immediately to discuss budget revision or remediation steps.",
            ],
            [
                'code'    => 'construction_project_completed',
                'name'    => 'Construction — Project Completed',
                'channel' => 'email',
                'subject' => 'Project Completed: {{project_name}}',
                'body'    => "Dear {{client_name}},\n\nWe are delighted to inform you that your construction project has been successfully completed.\n\nProject: {{project_name}}\nCompletion Date: {{actual_end_date}}\nContract Value: {{currency}} {{contract_value}}\nTotal Cost: {{currency}} {{total_spent}}\n\nPlease arrange a final inspection and sign-off at your earliest convenience.\n\nThank you for choosing our services.",
            ],
        ];

        foreach ($templates as $tpl) {
            CommTemplate::updateOrCreate(
                ['code' => $tpl['code']],
                array_merge($tpl, ['is_active' => true])
            );
        }
    }
}

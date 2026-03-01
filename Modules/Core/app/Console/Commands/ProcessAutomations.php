<?php

namespace Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\AutomationSetting;
use Modules\Core\Services\AutomationService;

class ProcessAutomations extends Command
{
    protected $signature = 'automations:process';

    protected $description = 'Process all scheduled automations';

    public function handle(AutomationService $automationService): void
    {
        $this->info('Processing scheduled automations...');

        $automations = AutomationSetting::enabled()->get();

        $processed = 0;
        $successful = 0;

        foreach ($automations as $automation) {
            if (! $automation->shouldRun()) {
                $this->line("Skipping: {$automation->module}.{$automation->action} - not scheduled to run yet");

                continue;
            }

            $this->line("Processing: {$automation->module}.{$automation->action} for company {$automation->company_id}");

            $result = $automationService->executeAutomation(
                $automation->module,
                $automation->action,
                $automation->company_id
            );

            if ($result) {
                $successful++;
                $this->info("✓ Success: {$automation->module}.{$automation->action}");
            } else {
                $this->error("✗ Failed: {$automation->module}.{$automation->action}");
            }

            $processed++;
        }

        $this->info("Processed {$processed} automations. Successful: {$successful}, Failed: ".($processed - $successful));
    }
}

<?php

namespace Modules\HR\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\HR\Services\LeaveAccrualService;

class ProcessLeaveCarryOverCommand extends Command
{
    protected $signature = 'hr:leave:carry-over 
                            {--from-year= : The year to carry over from (default: previous year)} 
                            {--to-year= : The year to carry over to (default: current year)} 
                            {--dry-run : Show what would be processed without making changes}';

    protected $description = 'Process year-end leave balance carry-over for all employees';

    public function handle(LeaveAccrualService $accrualService)
    {
        $fromYear = $this->option('from-year') ?? now()->subYear()->year;
        $toYear = $this->option('to-year') ?? now()->year;
        $dryRun = $this->option('dry-run');

        $this->info("Processing leave carry-over from {$fromYear} to {$toYear}");

        if ($dryRun) {
            $this->warn('DRY RUN: No changes will be made');
        }

        if ($this->confirm('Do you want to continue?', true)) {
            if ($dryRun) {
                $this->info('Dry run completed. Use without --dry-run to execute actual carry-over.');

                return;
            }

            $results = $accrualService->processYearEndCarryOver($fromYear, $toYear);

            $this->info("Processed: {$results['employees_processed']} employees");
            $this->info("Updated: {$results['balances_updated']} leave balances");

            if (! empty($results['errors'])) {
                $this->error('Errors encountered:');
                foreach ($results['errors'] as $error) {
                    $this->error("- {$error}");
                }
            } else {
                $this->info('Carry-over processing completed successfully!');
            }
        }
    }
}

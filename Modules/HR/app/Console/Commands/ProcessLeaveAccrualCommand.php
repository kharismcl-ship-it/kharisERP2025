<?php

namespace Modules\HR\Console\Commands;

use Illuminate\Console\Command;
use Modules\HR\Services\LeaveAccrualService;

class ProcessLeaveAccrualCommand extends Command
{
    protected $signature = 'hr:leave:accrue
                            {--year= : Year to accrue for (default: current year)}
                            {--month= : Month to accrue for (default: current month)}
                            {--dry-run : Show what would be processed without making changes}';

    protected $description = 'Process monthly leave accrual for all active employees';

    public function handle(LeaveAccrualService $accrualService): int
    {
        $year  = (int) ($this->option('year')  ?? now()->year);
        $month = (int) ($this->option('month') ?? now()->month);

        $this->info("Processing monthly leave accrual for {$year}-{$month}...");

        if ($this->option('dry-run')) {
            $this->warn('Dry-run mode — no changes will be saved.');
            return self::SUCCESS;
        }

        $results = $accrualService->processMonthlyAccrual($year, $month);

        $this->info("Employees processed: {$results['employees_processed']}");
        $this->info("Balances updated:    {$results['balances_updated']}");

        if (! empty($results['errors'])) {
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->error("  - {$error}");
            }
            return self::FAILURE;
        }

        $this->info('Monthly leave accrual completed successfully.');
        return self::SUCCESS;
    }
}

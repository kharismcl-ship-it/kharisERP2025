<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Services\EnhancedIntegrationService;

class SyncHRToExpensesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-hr {--limit=100 : Number of payroll records to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync HR payroll to finance expenses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(EnhancedIntegrationService $integrationService)
    {
        $limit = $this->option('limit');

        // This would be implemented when the HR module is fully developed
        // For now, we'll just show a message
        $this->info('HR payroll synchronization command registered.');
        $this->info('This command will be functional when the HR module is implemented.');
        $this->info('It will process payroll records and record them as expenses.');

        return 0;
    }
}

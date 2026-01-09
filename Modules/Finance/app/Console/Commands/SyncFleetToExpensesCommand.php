<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Services\EnhancedIntegrationService;

class SyncFleetToExpensesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-fleet {--limit=100 : Number of fleet records to process} {--type=fuel : Fleet record type (fuel|maintenance)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync fleet records to finance expenses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(EnhancedIntegrationService $integrationService)
    {
        $limit = $this->option('limit');
        $type = $this->option('type');

        // This would be implemented when the Fleet module is fully developed
        // For now, we'll just show a message
        $this->info('Fleet records synchronization command registered.');
        $this->info('This command will be functional when the Fleet module is implemented.');
        $this->info('It will process fleet records and record them as expenses.');
        $this->info("Selected record type: {$type}");

        return 0;
    }
}

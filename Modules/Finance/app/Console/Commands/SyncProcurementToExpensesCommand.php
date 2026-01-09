<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Services\EnhancedIntegrationService;

class SyncProcurementToExpensesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-procurement {--limit=100 : Number of purchase orders to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync procurement purchase orders to finance expenses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(EnhancedIntegrationService $integrationService)
    {
        $limit = $this->option('limit');

        // This would be implemented when the ProcurementInventory module is fully developed
        // For now, we'll just show a message
        $this->info('Procurement purchase orders synchronization command registered.');
        $this->info('This command will be functional when the ProcurementInventory module is implemented.');
        $this->info('It will process purchase orders and record them as expenses.');

        return 0;
    }
}

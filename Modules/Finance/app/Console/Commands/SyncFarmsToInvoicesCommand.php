<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Modules\Finance\Services\EnhancedIntegrationService;

class SyncFarmsToInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-farms {--limit=100 : Number of farm sales to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync farm sales to finance invoices';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(EnhancedIntegrationService $integrationService)
    {
        $limit = $this->option('limit');

        // This would be implemented when the Farms module is fully developed
        // For now, we'll just show a message
        $this->info('Farm sales synchronization command registered.');
        $this->info('This command will be functional when the Farms module is implemented.');
        $this->info('It will process farm sales and create corresponding invoices.');

        return 0;
    }
}

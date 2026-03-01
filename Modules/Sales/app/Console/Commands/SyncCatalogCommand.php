<?php

namespace Modules\Sales\Console\Commands;

use Illuminate\Console\Command;
use Modules\Sales\Services\CatalogSyncService;

class SyncCatalogCommand extends Command
{
    protected $signature   = 'sales:sync-catalog {--company= : Sync for a specific company ID}';
    protected $description = 'Sync all sellable items from source modules into SalesCatalog';

    public function handle(CatalogSyncService $service): int
    {
        $companyId = $this->option('company') ? (int) $this->option('company') : null;

        $this->info('Syncing catalog...');
        $service->syncAll($companyId);
        $this->info('Catalog sync complete.');

        return self::SUCCESS;
    }
}
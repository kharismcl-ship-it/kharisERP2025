<?php

namespace Modules\ProcurementInventory\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ProcurementInventory\Models\StockLot;

class ExpireStockLotsCommand extends Command
{
    protected $signature = 'procurement:expire-lots';

    protected $description = 'Mark stock lots as expired when their expiry date has passed';

    public function handle(): int
    {
        $count = StockLot::where('status', 'available')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->toDateString())
            ->count();

        if ($count === 0) {
            $this->info('No lots to expire.');
            return self::SUCCESS;
        }

        StockLot::where('status', 'available')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->toDateString())
            ->update(['status' => 'expired']);

        $this->info("Marked {$count} lot(s) as expired.");
        Log::info("[procurement:expire-lots] Expired {$count} stock lot(s).");

        return self::SUCCESS;
    }
}
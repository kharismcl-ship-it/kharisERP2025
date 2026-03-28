<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmNdviRecord;

class FetchNdviDataCommand extends Command
{
    protected $signature = 'farms:fetch-ndvi {--days=7 : Days between fetches}';

    protected $description = 'Fetch NDVI data from Open-Meteo / Sentinel Hub for all active farms';

    public function handle(): void
    {
        // This command is the hook for satellite API integration.
        // Current implementation: placeholder that creates a manual record reminder.
        // Future: replace with actual Sentinel Hub API call per farm_plot geometry.

        $farms = Farm::where('status', 'active')->get();
        $this->info("NDVI fetch hook for {$farms->count()} active farms.");
        $this->info('Configure FARMS_NDVI_API_KEY in .env and implement Sentinel Hub integration.');

        // Log that the command ran
        \Log::info('farms:fetch-ndvi ran for ' . $farms->count() . ' farms');
    }
}
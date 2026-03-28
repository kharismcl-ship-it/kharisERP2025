<?php

namespace Modules\Finance\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Modules\Finance\Models\FixedAsset;
use Modules\Finance\Models\FixedAssetDepreciationRun;

class RunDepreciationCommand extends Command
{
    protected $signature = 'finance:run-depreciation
                            {--company= : Company ID to restrict depreciation to}
                            {--period= : Period end date in YYYY-MM-DD format}';

    protected $description = 'Run monthly depreciation for all active fixed assets';

    public function handle(): int
    {
        $periodDate = $this->option('period')
            ? \Carbon\Carbon::parse($this->option('period'))
            : now()->endOfMonth();

        $companyId = $this->option('company');

        $query = FixedAsset::query()
            ->where('status', 'active')
            ->where('depreciation_method', '!=', 'none')
            ->whereNotNull('depreciation_method');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $assets = $query->get();

        if ($assets->isEmpty()) {
            $this->info('No assets found to depreciate.');
            return 0;
        }

        $this->info("Processing {$assets->count()} asset(s) for period: {$periodDate->toDateString()}");

        $bar = $this->output->createProgressBar($assets->count());
        $bar->start();

        foreach ($assets as $asset) {
            try {
                $monthlyDepreciation = $this->calculateMonthlyDepreciation($asset);

                if ($monthlyDepreciation <= 0) {
                    $bar->advance();
                    continue;
                }

                $newAccumulated = (float) $asset->accumulated_depreciation + $monthlyDepreciation;
                $maxDepreciation = (float) $asset->cost - (float) $asset->residual_value;

                if ($newAccumulated > $maxDepreciation) {
                    $monthlyDepreciation = $maxDepreciation - (float) $asset->accumulated_depreciation;
                    $newAccumulated = $maxDepreciation;
                }

                FixedAssetDepreciationRun::create([
                    'fixed_asset_id'     => $asset->id,
                    'period_end_date'    => $periodDate->toDateString(),
                    'amount'             => $monthlyDepreciation,
                    'accumulated_before' => $asset->accumulated_depreciation,
                    'accumulated_after'  => $newAccumulated,
                    'notes'              => "Method: {$asset->depreciation_method}",
                ]);

                $asset->update(['accumulated_depreciation' => $newAccumulated]);

                $this->line(" Asset {$asset->asset_code}: depreciated " . number_format($monthlyDepreciation, 2));
            } catch (\Exception $e) {
                $this->error(" Asset {$asset->id} failed: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Depreciation run complete.');

        return 0;
    }

    protected function calculateMonthlyDepreciation(FixedAsset $asset): float
    {
        $cost           = (float) $asset->cost;
        $residual       = (float) $asset->residual_value;
        $depreciableAmt = $cost - $residual;
        $accumulated    = (float) $asset->accumulated_depreciation;

        if ($accumulated >= $depreciableAmt) {
            return 0;
        }

        $usefulLifeYears = $asset->useful_life_years ?? $asset->useful_life ?? 5;

        return match ($asset->depreciation_method) {
            'straight_line' => $depreciableAmt / ($usefulLifeYears * 12),
            'declining_balance' => ($cost - $accumulated) * (2 / ($usefulLifeYears * 12)),
            default => 0,
        };
    }
}
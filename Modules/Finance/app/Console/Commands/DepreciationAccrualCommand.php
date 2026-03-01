<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\FixedAsset;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;

class DepreciationAccrualCommand extends Command
{
    protected $signature = 'finance:accrue-depreciation {--month= : Year-month to accrue (default: current month, e.g. 2026-03)}';

    protected $description = 'Post monthly depreciation journal entries for all active fixed assets';

    public function handle(): int
    {
        $monthStr  = $this->option('month') ?? now()->format('Y-m');
        $periodEnd = \Carbon\Carbon::parse($monthStr . '-01')->endOfMonth()->toDateString();

        $assets = FixedAsset::with('category')->active()->get();

        if ($assets->isEmpty()) {
            $this->info('No active fixed assets found.');
            return self::SUCCESS;
        }

        $count = 0;

        foreach ($assets as $asset) {
            if (! $asset->category || $asset->category->depreciation_method === 'none') {
                continue;
            }

            $monthly = $asset->monthlyDepreciation();

            if ($monthly <= 0) {
                continue;
            }

            // Skip if already fully depreciated
            if ((float) $asset->accumulated_depreciation >= ((float) $asset->cost - (float) $asset->residual_value)) {
                continue;
            }

            try {
                DB::transaction(function () use ($asset, $monthly, $periodEnd, $monthStr) {
                    $ref = 'DEP-' . $asset->asset_code . '-' . str_replace('-', '', $monthStr);

                    // Prevent duplicate posting for same asset/month
                    if (JournalEntry::where('reference', $ref)->exists()) {
                        return;
                    }

                    $entry = JournalEntry::create([
                        'company_id'  => $asset->company_id,
                        'date'        => $periodEnd,
                        'reference'   => $ref,
                        'description' => "Monthly depreciation — {$asset->name} ({$monthStr})",
                    ]);

                    // DR Depreciation Expense
                    $debitAccountId = $asset->category->depreciation_account_id;
                    if ($debitAccountId) {
                        JournalLine::create([
                            'journal_entry_id' => $entry->id,
                            'account_id'       => $debitAccountId,
                            'debit'            => $monthly,
                            'credit'           => 0,
                        ]);
                    }

                    // CR Accumulated Depreciation
                    $creditAccountId = $asset->category->accumulated_depreciation_account_id;
                    if ($creditAccountId) {
                        JournalLine::create([
                            'journal_entry_id' => $entry->id,
                            'account_id'       => $creditAccountId,
                            'debit'            => 0,
                            'credit'           => $monthly,
                        ]);
                    }

                    // Update accumulated depreciation on asset
                    $newAccum = min(
                        (float) $asset->accumulated_depreciation + $monthly,
                        (float) $asset->cost - (float) $asset->residual_value
                    );

                    $asset->update(['accumulated_depreciation' => $newAccum]);
                });

                $count++;
            } catch (\Throwable $e) {
                Log::error('Depreciation accrual failed', ['asset_id' => $asset->id, 'error' => $e->getMessage()]);
            }
        }

        $this->info("Depreciation accrued for {$count} asset(s) for {$monthStr}.");

        return self::SUCCESS;
    }
}

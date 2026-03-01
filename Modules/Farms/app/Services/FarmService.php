<?php

namespace Modules\Farms\Services;

use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\LivestockBatch;

class FarmService
{
    /**
     * Mark a crop cycle as harvested and record the harvest.
     */
    public function recordHarvest(CropCycle $cycle, array $harvestData): HarvestRecord
    {
        $record = HarvestRecord::create(array_merge($harvestData, [
            'farm_id'       => $cycle->farm_id,
            'crop_cycle_id' => $cycle->id,
            'company_id'    => $cycle->company_id,
        ]));

        // Auto-mark cycle as harvested when first harvest is recorded
        if ($cycle->status !== 'harvested') {
            $cycle->update([
                'status'              => 'harvested',
                'actual_harvest_date' => $record->harvest_date,
            ]);
        }

        return $record;
    }

    /**
     * Update livestock batch count (mortality, sales, etc.).
     */
    public function updateLivestockCount(LivestockBatch $batch, int $newCount, string $reason = ''): void
    {
        $batch->update(['current_count' => $newCount]);

        if ($newCount === 0) {
            $batch->update(['status' => 'sold']);
        }
    }

    /**
     * Total farm revenue (all harvest records).
     */
    public function totalRevenue(Farm $farm, ?string $from = null, ?string $to = null): float
    {
        $query = $farm->harvestRecords();
        if ($from && $to) {
            $query->whereBetween('harvest_date', [$from, $to]);
        }
        return (float) $query->sum('total_revenue');
    }

    /**
     * Total farm expenses.
     */
    public function totalExpenses(Farm $farm, ?string $from = null, ?string $to = null): float
    {
        $query = $farm->expenses();
        if ($from && $to) {
            $query->whereBetween('expense_date', [$from, $to]);
        }
        return (float) $query->sum('amount');
    }

    /**
     * Net profit for a farm within a date range.
     */
    public function netProfit(Farm $farm, ?string $from = null, ?string $to = null): float
    {
        return $this->totalRevenue($farm, $from, $to) - $this->totalExpenses($farm, $from, $to);
    }
}

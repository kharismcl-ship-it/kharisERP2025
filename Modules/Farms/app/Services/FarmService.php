<?php

namespace Modules\Farms\Services;

use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmExpense;
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

    /**
     * Full P&L breakdown for a single crop cycle.
     */
    public function cropCyclePnL(CropCycle $cycle): array
    {
        $revenue       = (float) $cycle->harvestRecords()->sum('total_revenue');
        $inputCost     = (float) $cycle->inputApplications()->sum('total_cost');
        $activityCost  = (float) $cycle->activities()->sum('cost');
        $otherExpense  = (float) FarmExpense::where('crop_cycle_id', $cycle->id)->sum('amount');
        $totalCost     = $inputCost + $activityCost + $otherExpense;
        $netProfit     = $revenue - $totalCost;
        $totalHarvested = (float) $cycle->harvestRecords()->sum('quantity');
        $yieldPct      = ($cycle->expected_yield && $cycle->expected_yield > 0)
            ? round(($totalHarvested / $cycle->expected_yield) * 100, 1)
            : null;

        return compact('revenue', 'inputCost', 'activityCost', 'otherExpense', 'totalCost', 'netProfit', 'yieldPct');
    }

    /**
     * Yield achievement as a percentage: actual / expected * 100.
     */
    public function yieldVsTarget(CropCycle $cycle): ?float
    {
        if (! $cycle->expected_yield || $cycle->expected_yield <= 0) {
            return null;
        }
        $actual = (float) $cycle->harvestRecords()->sum('quantity');
        return round(($actual / $cycle->expected_yield) * 100, 1);
    }

    /**
     * Cost per unit of harvest output.
     */
    public function costPerUnit(CropCycle $cycle): ?float
    {
        $totalHarvested = (float) $cycle->harvestRecords()->sum('quantity');
        if ($totalHarvested <= 0) {
            return null;
        }
        $pnl = $this->cropCyclePnL($cycle);
        return round($pnl['totalCost'] / $totalHarvested, 4);
    }

    /**
     * Recent health summary for a livestock batch.
     */
    public function livestockHealthSummary(LivestockBatch $batch): array
    {
        $recentEvents = $batch->healthRecords()
            ->orderByDesc('event_date')
            ->limit(5)
            ->get();

        $nextDue = $batch->healthRecords()
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '>=', now()->toDateString())
            ->orderBy('next_due_date')
            ->first();

        return ['recent_events' => $recentEvents, 'next_due' => $nextDue];
    }

    /**
     * Average daily weight gain (kg/day) from weight records.
     */
    public function livestockGrowthRate(LivestockBatch $batch): ?float
    {
        $records = $batch->weightRecords()->orderBy('record_date')->get();
        if ($records->count() < 2) {
            return null;
        }
        $first = $records->first();
        $last  = $records->last();
        $days  = $first->record_date->diffInDays($last->record_date);
        if ($days <= 0) {
            return null;
        }
        return round(($last->avg_weight_kg - $first->avg_weight_kg) / $days, 4);
    }

    /**
     * Feed conversion ratio: total feed kg / total weight gained kg.
     */
    public function feedConversionRatio(LivestockBatch $batch): ?float
    {
        $totalFeedKg    = (float) $batch->feedRecords()->sum('quantity_kg');
        $weightGained   = null;
        $records = $batch->weightRecords()->orderBy('record_date')->get();
        if ($records->count() >= 2) {
            $weightGained = $records->last()->avg_weight_kg - $records->first()->avg_weight_kg;
        }
        if (! $totalFeedKg || ! $weightGained || $weightGained <= 0) {
            return null;
        }
        return round($totalFeedKg / $weightGained, 2);
    }
}

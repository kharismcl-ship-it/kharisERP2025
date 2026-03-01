<?php

namespace Modules\ManufacturingWater\Services;

use Modules\ManufacturingWater\Models\MwPlant;

class ManufacturingWaterService
{
    /**
     * Total distribution volume for a plant in a date range.
     */
    public function totalDistributed(MwPlant $plant, ?string $from = null, ?string $to = null): float
    {
        $query = $plant->distributionRecords();

        if ($from) {
            $query->where('distribution_date', '>=', $from);
        }
        if ($to) {
            $query->where('distribution_date', '<=', $to);
        }

        return (float) $query->sum('volume_liters');
    }

    /**
     * Total distribution revenue for a plant.
     */
    public function totalRevenue(MwPlant $plant, ?string $from = null, ?string $to = null): float
    {
        $query = $plant->distributionRecords();

        if ($from) {
            $query->where('distribution_date', '>=', $from);
        }
        if ($to) {
            $query->where('distribution_date', '<=', $to);
        }

        return (float) $query->sum('total_amount');
    }

    /**
     * Total chemical cost for a plant.
     */
    public function totalChemicalCost(MwPlant $plant): float
    {
        return (float) $plant->chemicalUsages()->sum('total_cost');
    }

    /**
     * Water quality pass rate for a plant.
     */
    public function qualityPassRate(MwPlant $plant): ?float
    {
        $total  = $plant->waterTestRecords()->count();
        $passed = $plant->waterTestRecords()->where('passed', true)->count();

        if ($total === 0) {
            return null;
        }

        return round(($passed / $total) * 100, 1);
    }

    /**
     * Current tank fill percentage across all tanks at a plant.
     */
    public function averageTankFillPercent(MwPlant $plant): ?float
    {
        $tanks = $plant->tankLevels()->latest('recorded_at')->get()->unique('tank_name');

        if ($tanks->isEmpty()) {
            return null;
        }

        $totalCapacity = $tanks->sum('capacity_liters');
        $totalCurrent  = $tanks->sum('current_level_liters');

        if ($totalCapacity == 0) {
            return null;
        }

        return round(($totalCurrent / $totalCapacity) * 100, 1);
    }
}
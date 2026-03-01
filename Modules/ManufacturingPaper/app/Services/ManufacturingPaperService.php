<?php

namespace Modules\ManufacturingPaper\Services;

use Modules\ManufacturingPaper\Models\MpProductionBatch;
use Modules\ManufacturingPaper\Models\MpPlant;

class ManufacturingPaperService
{
    /**
     * Start a production batch — sets status to in_progress and records start time.
     */
    public function startBatch(MpProductionBatch $batch): void
    {
        $batch->update([
            'status'     => 'in_progress',
            'start_time' => now(),
        ]);
    }

    /**
     * Complete a production batch — records end time and final quantity.
     */
    public function completeBatch(MpProductionBatch $batch, float $quantityProduced, float $wasteQuantity = 0): void
    {
        $batch->update([
            'status'             => 'completed',
            'end_time'           => now(),
            'quantity_produced'  => $quantityProduced,
            'waste_quantity'     => $wasteQuantity,
        ]);

        // Finance integration hook — record production cost as journal entry
        if (class_exists('Modules\\Finance\\Services\\JournalService') && $batch->production_cost) {
            try {
                // Placeholder: post production cost to finance
            } catch (\Throwable) {}
        }
    }

    /**
     * Total production volume for a plant (completed batches only).
     */
    public function totalProductionVolume(MpPlant $plant, string $unit = 'tonnes'): float
    {
        return (float) $plant->productionBatches()
            ->where('status', 'completed')
            ->where('unit', $unit)
            ->sum('quantity_produced');
    }

    /**
     * Total waste volume for a plant.
     */
    public function totalWasteVolume(MpPlant $plant): float
    {
        return (float) $plant->productionBatches()
            ->where('status', 'completed')
            ->sum('waste_quantity');
    }

    /**
     * Overall equipment efficiency for a plant — production vs planned.
     */
    public function overallEfficiency(MpPlant $plant): ?float
    {
        $planned  = (float) $plant->productionBatches()->where('status', 'completed')->sum('quantity_planned');
        $produced = (float) $plant->productionBatches()->where('status', 'completed')->sum('quantity_produced');

        if ($planned == 0) {
            return null;
        }

        return round(($produced / $planned) * 100, 1);
    }

    /**
     * Pass rate for quality records at a plant.
     */
    public function qualityPassRate(MpPlant $plant): ?float
    {
        $total  = $plant->productionBatches()->withCount('qualityRecords')->get()->sum('quality_records_count');
        $passed = $plant->productionBatches()->with('qualityRecords')->get()
            ->flatMap(fn ($b) => $b->qualityRecords)
            ->where('passed', true)
            ->count();

        if ($total == 0) {
            return null;
        }

        return round(($passed / $total) * 100, 1);
    }
}

<?php

namespace Modules\ProcurementInventory\Services;

use Illuminate\Support\Facades\Auth;
use Modules\ProcurementInventory\Models\CycleCount;
use Modules\ProcurementInventory\Models\CycleCountLine;
use Modules\ProcurementInventory\Models\StockLevel;

class CycleCountService
{
    public function __construct(
        protected StockService $stockService,
    ) {}

    /**
     * Create a cycle count and populate lines from current stock levels.
     *
     * @param  array    $data    CycleCount fillable fields
     * @param  int[]    $itemIds Specific item IDs to count (empty = all items in warehouse)
     */
    public function createCount(array $data, array $itemIds = []): CycleCount
    {
        $count = CycleCount::create($data);

        $query = StockLevel::where('company_id', $count->company_id)
            ->with('item');

        if ($count->warehouse_id) {
            $query->where('warehouse_id', $count->warehouse_id);
        }

        if (! empty($itemIds)) {
            $query->whereIn('item_id', $itemIds);
        }

        $stockLevels = $query->get();

        foreach ($stockLevels as $sl) {
            CycleCountLine::create([
                'count_id'        => $count->id,
                'item_id'         => $sl->item_id,
                'warehouse_id'    => $sl->warehouse_id,
                'system_quantity' => (float) $sl->quantity_on_hand,
                'status'          => 'pending',
            ]);
        }

        return $count->load('lines');
    }

    /**
     * Submit counted quantities for a cycle count.
     *
     * @param CycleCount $count
     * @param array<int, float> $counts  [ line_id => counted_qty ]
     */
    public function submitCounts(CycleCount $count, array $counts): void
    {
        foreach ($counts as $lineId => $countedQty) {
            $line = CycleCountLine::where('count_id', $count->id)
                ->where('id', $lineId)
                ->first();

            if ($line) {
                $line->update(['counted_quantity' => (float) $countedQty]);
            }
        }

        $count->update([
            'status'              => 'completed',
            'counted_date'        => now()->toDateString(),
            'counted_by_user_id'  => Auth::id(),
        ]);
    }

    /**
     * Apply stock adjustments for all approved/counted lines with variances.
     */
    public function applyAdjustments(CycleCount $count): void
    {
        $lines = $count->lines()
            ->whereIn('status', ['counted', 'approved'])
            ->whereNotNull('variance')
            ->where('variance', '!=', 0)
            ->get();

        foreach ($lines as $line) {
            $this->stockService->adjust(
                $count->company_id,
                $line->item_id,
                (float) $line->variance,
                "Cycle count adjustment — {$count->count_number}",
                Auth::id(),
                $line->warehouse_id,
            );

            $line->update(['status' => 'adjusted']);
        }

        // Mark count as fully completed
        $allAdjusted = $count->lines()->where('variance', '!=', 0)->where('status', '!=', 'adjusted')->doesntExist();
        if ($allAdjusted) {
            $count->update(['status' => 'completed']);
        }
    }
}
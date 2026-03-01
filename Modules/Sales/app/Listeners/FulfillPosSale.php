<?php

namespace Modules\Sales\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Sales\Events\PosSaleCompleted;

class FulfillPosSale
{
    public function handle(PosSaleCompleted $event): void
    {
        $sale = $event->sale;
        $sale->load('lines.catalogItem');

        foreach ($sale->lines as $line) {
            $sourceModule = $line->catalogItem->source_module ?? null;

            if ($sourceModule === 'ProcurementInventory' && $line->catalogItem->source_id) {
                if (class_exists(\Modules\ProcurementInventory\Models\StockLevel::class)) {
                    try {
                        \Modules\ProcurementInventory\Models\StockLevel::where('item_id', $line->catalogItem->source_id)
                            ->where('company_id', optional($sale->session->terminal)->company_id)
                            ->decrement('quantity_on_hand', $line->quantity);
                    } catch (\Throwable $e) {
                        Log::warning("FulfillPosSale stock decrement failed for line {$line->id}: {$e->getMessage()}");
                    }
                }
            }
        }
    }
}
<?php

namespace Modules\ProcurementInventory\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ProcurementInventory\Events\StockLevelLow;

class SendLowStockAlert
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(StockLevelLow $event): void
    {
        $stock = $event->stockLevel;
        $item  = $event->item;

        $data = [
            'item_name'         => $item->name,
            'item_code'         => $item->code ?? $item->sku ?? 'N/A',
            'quantity_on_hand'  => (float) $stock->quantity_on_hand,
            'reorder_level'     => (float) ($item->reorder_level ?? 0),
            'reorder_quantity'  => (float) ($item->reorder_quantity ?? 0),
            'uom'               => $item->uom ?? 'units',
            'alert_date'        => now()->format('d M Y'),
        ];

        try {
            $this->comms->sendToModel(
                $stock,
                'email',
                'procurement_low_stock_alert',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendLowStockAlert failed', [
                'item_id'        => $item->id,
                'stock_level_id' => $stock->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
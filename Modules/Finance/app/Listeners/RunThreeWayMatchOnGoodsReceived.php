<?php

namespace Modules\Finance\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Services\ThreeWayMatchService;
use Modules\ProcurementInventory\Events\GoodsReceived;

class RunThreeWayMatchOnGoodsReceived
{
    public function __construct(protected ThreeWayMatchService $matchService) {}

    public function handle(GoodsReceived $event): void
    {
        $grn = $event->goodsReceipt;

        // Find any pending vendor invoices linked to the same PO
        $invoices = Invoice::where('type', 'vendor')
            ->where('purchase_order_id', $grn->purchase_order_id)
            ->whereIn('match_status', ['pending', 'qty_variance', 'price_variance'])
            ->get();

        foreach ($invoices as $invoice) {
            // Link the GRN to the invoice if not already linked
            if (! $invoice->grn_id) {
                $invoice->update(['grn_id' => $grn->id]);
            }

            try {
                $status = $this->matchService->match($invoice);
                Log::info("Three-way match: Invoice #{$invoice->id} → {$status}");
            } catch (\Throwable $e) {
                Log::error("Three-way match failed for Invoice #{$invoice->id}: " . $e->getMessage());
            }
        }
    }
}

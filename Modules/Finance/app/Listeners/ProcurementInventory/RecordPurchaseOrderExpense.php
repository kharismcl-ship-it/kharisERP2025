<?php

namespace Modules\Finance\Listeners\ProcurementInventory;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Services\EnhancedIntegrationService;
use Modules\ProcurementInventory\Events\PurchaseOrderApproved;

class RecordPurchaseOrderExpense
{
    public function handle(PurchaseOrderApproved $event): void
    {
        $po = $event->purchaseOrder->load('vendor', 'lines');

        if ($po->finance_invoice_id) {
            Log::info('RecordPurchaseOrderExpense: AP invoice already exists', [
                'po_id'              => $po->id,
                'finance_invoice_id' => $po->finance_invoice_id,
            ]);

            return;
        }

        try {
            app(EnhancedIntegrationService::class)->recordProcurementExpense($po);

            Log::info('RecordPurchaseOrderExpense: AP invoice and journal created', [
                'po_id'     => $po->id,
                'po_number' => $po->po_number,
            ]);
        } catch (\Exception $e) {
            Log::error('RecordPurchaseOrderExpense: failed', [
                'po_id' => $po->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

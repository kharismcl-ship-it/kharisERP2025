<?php

namespace Modules\ProcurementInventory\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\PurchaseOrderLine;

/**
 * Shared helper for creating a draft PO from cross-module events.
 * Not a listener itself — used by the specific event listeners below.
 */
class CreateDraftPurchaseOrder
{
    /**
     * @param  int     $companyId
     * @param  string  $notes        Human-readable context for the PO
     * @param  array   $lines        [{description, quantity, unit_price, unit_of_measure?}]
     * @param  array   $extra        Extra PO columns (hostel_id, project_id, farm_id, module_tag)
     */
    public function create(int $companyId, string $notes, array $lines, array $extra = []): ?PurchaseOrder
    {
        if (empty($lines)) {
            return null;
        }

        try {
            $po = PurchaseOrder::create(array_merge([
                'company_id'   => $companyId,
                'vendor_id'    => null, // must be filled in manually
                'po_date'      => now()->toDateString(),
                'status'       => 'draft',
                'currency'     => 'GHS',
                'notes'        => $notes,
            ], $extra));

            foreach ($lines as $line) {
                PurchaseOrderLine::create([
                    'purchase_order_id' => $po->id,
                    'description'       => $line['description'],
                    'quantity'          => $line['quantity'] ?? 1,
                    'unit_of_measure'   => $line['unit_of_measure'] ?? null,
                    'unit_price'        => $line['unit_price'] ?? 0,
                    'tax_rate'          => 0,
                ]);
            }

            $po->recalculateTotals();

            return $po;
        } catch (\Throwable $e) {
            Log::error('CreateDraftPurchaseOrder failed: ' . $e->getMessage());
            return null;
        }
    }
}

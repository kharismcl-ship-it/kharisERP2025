<?php

namespace Modules\Construction\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Construction\Events\ContractorRequestDecided;
use Modules\Finance\Models\Invoice;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\PurchaseOrderLine;

class AutoCreatePOOrInvoiceOnApproval
{
    public function handle(ContractorRequestDecided $event): void
    {
        $request  = $event->request;
        $decision = $event->decision;

        if ($decision !== 'approved') {
            return;
        }

        try {
            if (in_array($request->request_type, ['materials', 'equipment'])) {
                $this->createPurchaseOrder($request);
            } elseif ($request->request_type === 'funds') {
                $this->createInvoice($request);
            }
        } catch (\Throwable $e) {
            Log::warning('AutoCreatePOOrInvoiceOnApproval failed', [
                'request_id' => $request->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    private function createPurchaseOrder($request): void
    {
        $contractor = $request->contractor;

        $po = PurchaseOrder::create([
            'company_id'  => $request->company_id,
            'status'      => 'draft',
            'module_tag'  => 'construction',
            'project_id'  => $request->construction_project_id,
            'notes'       => "Auto-created from Contractor Request #{$request->id}: {$request->title}",
            'po_date'     => now()->toDateString(),
            'currency'    => 'GHS',
        ]);

        foreach ($request->items as $item) {
            PurchaseOrderLine::create([
                'purchase_order_id' => $po->id,
                'item_id'           => $item->item_id,
                'description'       => $item->material_name,
                'unit_of_measure'   => $item->unit,
                'quantity'          => $item->quantity,
                'unit_price'        => $item->unit_cost ?? 0,
                'line_total'        => $item->total_cost ?? 0,
            ]);
        }

        $request->updateQuietly(['procurement_po_id' => $po->id]);
    }

    private function createInvoice($request): void
    {
        $contractor = $request->contractor;

        $invoice = Invoice::create([
            'company_id'              => $request->company_id,
            'type'                    => 'vendor',
            'customer_name'           => $contractor?->name ?? 'Unknown Contractor',
            'total'                   => $request->approved_amount ?? $request->requested_amount ?? 0,
            'construction_project_id' => $request->construction_project_id,
            'module'                  => 'construction',
            'entity_type'             => 'ContractorRequest',
            'entity_id'               => $request->id,
            'status'                  => 'draft',
            'invoice_date'            => now()->toDateString(),
        ]);

        $request->updateQuietly(['finance_invoice_id' => $invoice->id]);
    }
}

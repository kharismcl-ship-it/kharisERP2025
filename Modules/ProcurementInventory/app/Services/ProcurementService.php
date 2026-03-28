<?php

namespace Modules\ProcurementInventory\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ProcurementInventory\Events\GoodsReceived;
use Modules\ProcurementInventory\Events\PurchaseOrderApproved;
use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Models\GoodsReceiptLine;
use Modules\ProcurementInventory\Models\ProcurementApprovalRule;
use Modules\ProcurementInventory\Models\ProcurementInvoiceMatch;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\PurchaseOrderLine;

class ProcurementService
{
    public function __construct(
        protected StockService $stockService,
        protected VendorPerformanceService $vendorPerformanceService,
    ) {}

    /**
     * Submit a draft PO for approval.
     */
    public function submit(PurchaseOrder $po): PurchaseOrder
    {
        if (! $po->canBeSubmitted()) {
            throw new \Exception("PO {$po->po_number} cannot be submitted in status: {$po->status}");
        }

        $po->submit();

        return $po->fresh();
    }

    /**
     * Approve a submitted PO.
     */
    public function approve(PurchaseOrder $po): PurchaseOrder
    {
        if (! $po->canBeApproved()) {
            throw new \Exception("PO {$po->po_number} cannot be approved in status: {$po->status}");
        }

        // DoA check: if approval rules exist for this company, enforce them
        $rules = ProcurementApprovalRule::matchingRules($po);
        if ($rules->isNotEmpty()) {
            $authUserId = Auth::id();
            $allowedApproverIds = $rules->pluck('approver_user_id')->filter()->unique();
            if (! $allowedApproverIds->contains($authUserId)) {
                $approverNames = $rules->map(fn ($r) => optional($r->approver)->name ?? "User #{$r->approver_user_id}")->implode(', ');
                throw new \Exception("Approval requires: {$approverNames}");
            }
        }

        DB::transaction(function () use ($po) {
            $po->approve();
            // Track ordered qty against stock
            $this->stockService->incrementOnOrder($po->load('lines'));
        });

        event(new PurchaseOrderApproved($po->fresh()));

        return $po->fresh();
    }

    /**
     * Mark a PO as ordered (sent to vendor).
     */
    public function markOrdered(PurchaseOrder $po): PurchaseOrder
    {
        if ($po->status !== 'approved') {
            throw new \Exception("PO {$po->po_number} must be approved before marking as ordered.");
        }

        $po->markOrdered();

        return $po->fresh();
    }

    /**
     * Cancel a PO (draft/submitted/approved only).
     */
    public function cancel(PurchaseOrder $po): PurchaseOrder
    {
        DB::transaction(function () use ($po) {
            if (in_array($po->status, ['approved', 'ordered'])) {
                $this->stockService->decrementOnOrder($po->load('lines'));
            }
            $po->cancel();
        });

        return $po->fresh();
    }

    /**
     * Create and confirm a GoodsReceipt against a PO.
     * Accepts an array of ['purchase_order_line_id' => X, 'quantity_received' => Y, 'notes' => '...']
     */
    public function receiveGoods(PurchaseOrder $po, array $receivedLines, ?string $notes = null): GoodsReceipt
    {
        if (! $po->canReceiveGoods()) {
            throw new \Exception("PO {$po->po_number} is not in a receivable state: {$po->status}");
        }

        return DB::transaction(function () use ($po, $receivedLines, $notes) {
            // Create GRN header
            $grn = GoodsReceipt::create([
                'company_id'        => $po->company_id,
                'purchase_order_id' => $po->id,
                'vendor_id'         => $po->vendor_id,
                'receipt_date'      => now()->toDateString(),
                'received_by'       => Auth::id(),
                'status'            => 'draft',
                'notes'             => $notes,
            ]);

            // Create GRN lines
            foreach ($receivedLines as $received) {
                $poLine = PurchaseOrderLine::findOrFail($received['purchase_order_line_id']);

                if ((float) $received['quantity_received'] <= 0) {
                    continue;
                }

                GoodsReceiptLine::create([
                    'goods_receipt_id'       => $grn->id,
                    'purchase_order_line_id' => $poLine->id,
                    'item_id'                => $poLine->item_id,
                    'quantity_ordered'       => $poLine->quantity,
                    'quantity_received'      => $received['quantity_received'],
                    'unit_of_measure'        => $poLine->unit_of_measure,
                    'unit_price'             => $poLine->unit_price,
                    'notes'                  => $received['notes'] ?? null,
                ]);

                // Update received qty on PO line
                $poLine->increment('quantity_received', (float) $received['quantity_received']);
            }

            // Confirm the GRN
            $grn->update(['status' => 'confirmed']);

            // Update stock levels
            $this->stockService->updateFromReceipt($grn->load('lines'));

            // Update PO receipt status
            $po->load('lines');
            $po->updateReceiptStatus();

            event(new GoodsReceived($grn->fresh()));

            // Create Finance payable invoice when PO is fully/partially received
            $this->createFinanceInvoice($po->fresh());

            // Create 3-Way Match record
            $freshGrn = $grn->fresh()->load('lines');
            $freshPo  = $po->fresh();
            $grnTotal = $freshGrn->lines->sum(fn ($l) => (float) $l->quantity_received * (float) $l->unit_price);
            $poTotal  = (float) $freshPo->total;
            $variance = abs($poTotal - $grnTotal);
            $tolerancePct = 2.0;
            $matchStatus = $poTotal > 0 && ($variance / max($poTotal, 0.01)) * 100 <= $tolerancePct
                ? 'matched'
                : 'po_grn_mismatch';

            ProcurementInvoiceMatch::create([
                'company_id'        => $freshPo->company_id,
                'purchase_order_id' => $freshPo->id,
                'goods_receipt_id'  => $freshGrn->id,
                'po_total'          => $poTotal,
                'grn_total'         => $grnTotal,
                'po_grn_variance'   => $poTotal - $grnTotal,
                'status'            => $matchStatus,
                'matched_at'        => $matchStatus === 'matched' ? now() : null,
            ]);

            // Record vendor performance
            $this->vendorPerformanceService->recordFromGrn($freshGrn);

            return $grn->fresh();
        });
    }

    /**
     * Integrate with Finance module to create a payable invoice on receipt.
     */
    protected function createFinanceInvoice(PurchaseOrder $po): void
    {
        if (! class_exists('\\Modules\\Finance\\Services\\EnhancedIntegrationService')) {
            return;
        }

        try {
            app('\\Modules\\Finance\\Services\\EnhancedIntegrationService')
                ->recordProcurementExpense($po);
        } catch (\Exception $e) {
            Log::warning('Failed to create Finance invoice for PO', [
                'po_id'    => $po->id,
                'po_number'=> $po->po_number,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}

<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\Invoice;

class ThreeWayMatchService
{
    /**
     * Tolerance for price variance (percentage).
     * E.g. 0.02 = 2% tolerance before flagging as price_variance.
     */
    protected float $priceTolerancePct = 0.02;

    /**
     * Tolerance for quantity variance (percentage).
     * E.g. 0.01 = 1% tolerance (rounding differences).
     */
    protected float $qtyTolerancePct = 0.01;

    /**
     * Run three-way match against a vendor invoice linked to a PO and GRN.
     *
     * Returns the updated match_status string.
     */
    public function match(Invoice $invoice): string
    {
        if ($invoice->type !== 'vendor' || ! $invoice->purchase_order_id) {
            $invoice->update(['match_status' => 'not_applicable']);
            return 'not_applicable';
        }

        if (! class_exists(\Modules\ProcurementInventory\Models\PurchaseOrder::class)) {
            $invoice->update(['match_status' => 'pending']);
            return 'pending';
        }

        $po = \Modules\ProcurementInventory\Models\PurchaseOrder::with('lines')->find($invoice->purchase_order_id);
        if (! $po) {
            $invoice->update(['match_status' => 'pending']);
            return 'pending';
        }

        // --- Price check: compare invoice total to PO total ---
        $poTotal      = (float) $po->total;
        $invoiceTotal = (float) $invoice->total;
        $priceDiff    = abs($invoiceTotal - $poTotal);
        $priceVariancePct = $poTotal > 0 ? ($priceDiff / $poTotal) : 0;

        // --- Qty check: if GRN linked, compare GRN received total to invoice total ---
        $qtyStatus = 'matched';
        if ($invoice->grn_id && class_exists(\Modules\ProcurementInventory\Models\GoodsReceipt::class)) {
            $grn = \Modules\ProcurementInventory\Models\GoodsReceipt::with('lines')->find($invoice->grn_id);
            if ($grn) {
                $grnTotal = $grn->lines->sum(function ($line) {
                    return (float) $line->quantity_received * (float) $line->unit_price;
                });
                $qtyDiff    = abs($invoiceTotal - $grnTotal);
                $qtyVarPct  = $grnTotal > 0 ? ($qtyDiff / $grnTotal) : 0;
                if ($qtyVarPct > $this->qtyTolerancePct) {
                    $qtyStatus = 'qty_variance';
                }
            }
        }

        // Determine final status
        if ($priceVariancePct > $this->priceTolerancePct) {
            $matchStatus     = 'price_variance';
            $varianceAmount  = round($priceDiff, 2);
        } elseif ($qtyStatus === 'qty_variance') {
            $matchStatus    = 'qty_variance';
            $varianceAmount = null;
        } else {
            $matchStatus    = 'matched';
            $varianceAmount = null;
        }

        $invoice->update([
            'match_status'           => $matchStatus,
            'match_variance_amount'  => $varianceAmount,
        ]);

        return $matchStatus;
    }

    /**
     * Override match to 'exception' with a note.
     */
    public function escalate(Invoice $invoice, string $note): void
    {
        $invoice->update([
            'match_status' => 'exception',
            'match_notes'  => $note,
        ]);
    }
}

<?php

namespace Modules\ProcurementInventory\Services;

use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Models\VendorPerformanceRecord;
use Modules\ProcurementInventory\Models\VendorScorecard;

class VendorPerformanceService
{
    public function recordFromGrn(GoodsReceipt $receipt): void
    {
        $po = $receipt->purchaseOrder;
        if (! $po) {
            return;
        }

        $lines = $receipt->lines()->with('item')->get();
        if ($lines->isEmpty()) {
            return;
        }

        // Calculate aggregated figures across all lines
        $totalOrdered  = $lines->sum(fn ($l) => (float) $l->quantity_ordered);
        $totalReceived = $lines->sum(fn ($l) => (float) $l->quantity_received);
        $totalRejected = $lines->sum(fn ($l) => (float) ($l->quantity_rejected ?? 0));
        $poUnitPrice   = $lines->avg(fn ($l) => (float) $l->unit_price) ?: 0;
        $grnUnitPrice  = $poUnitPrice; // same line items, use PO price as GRN price
        $qualityRate   = $totalOrdered > 0
            ? (($totalReceived - $totalRejected) / max($totalOrdered, 0.0001)) * 100
            : 100.0;

        $priceVariancePct = $poUnitPrice > 0
            ? (($grnUnitPrice - $poUnitPrice) / $poUnitPrice) * 100
            : 0.0;

        // Days late
        $expectedDate = $po->expected_delivery_date;
        $actualDate   = $receipt->receipt_date;
        $daysLate     = 0;
        if ($expectedDate && $actualDate) {
            $daysLate = (int) $expectedDate->diffInDays($actualDate, false);
        }

        VendorPerformanceRecord::create([
            'company_id'             => $receipt->company_id,
            'vendor_id'              => $po->vendor_id,
            'purchase_order_id'      => $po->id,
            'goods_receipt_id'       => $receipt->id,
            'expected_delivery_date' => $expectedDate,
            'actual_delivery_date'   => $actualDate,
            'days_late'              => $daysLate,
            'quantity_ordered'       => $totalOrdered,
            'quantity_received'      => $totalReceived,
            'quantity_rejected'      => $totalRejected,
            'quality_rate'           => round($qualityRate, 2),
            'po_unit_price'          => $poUnitPrice,
            'grn_unit_price'         => $grnUnitPrice,
            'price_variance_pct'     => round($priceVariancePct, 2),
        ]);

        // Recalculate scorecard for the current month
        $this->recalculateScorecard($po->vendor_id, $receipt->company_id, now()->year, now()->month);
    }

    protected function recalculateScorecard(int $vendorId, int $companyId, int $year, int $month): void
    {
        $records = VendorPerformanceRecord::where('vendor_id', $vendorId)
            ->where('company_id', $companyId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        if ($records->isEmpty()) {
            return;
        }

        $totalOrders = $records->count();
        $onTimeCount = $records->filter(fn ($r) => $r->days_late <= 0)->count();
        $onTimeRate  = $totalOrders > 0 ? ($onTimeCount / $totalOrders) * 100 : 0;

        $avgQualityRate      = $records->avg(fn ($r) => (float) $r->quality_rate);
        $avgPriceVariancePct = $records->avg(fn ($r) => abs((float) $r->price_variance_pct));

        // Weighted: on_time 40% + quality 40% + price 20%
        // Price score: 100 - avg_variance (capped 0-100)
        $priceScore   = max(0, 100 - ($avgPriceVariancePct ?? 0));
        $overallScore = ($onTimeRate * 0.4) + ($avgQualityRate * 0.4) + ($priceScore * 0.2);

        VendorScorecard::updateOrCreate(
            [
                'company_id'   => $companyId,
                'vendor_id'    => $vendorId,
                'period_year'  => $year,
                'period_month' => $month,
            ],
            [
                'total_orders'           => $totalOrders,
                'on_time_rate'           => round($onTimeRate, 2),
                'avg_quality_rate'       => round((float) $avgQualityRate, 2),
                'avg_price_variance_pct' => round((float) $avgPriceVariancePct, 2),
                'overall_score'          => round($overallScore, 2),
            ]
        );
    }

    public function getScorecard(int $vendorId, int $companyId, int $year, int $month): VendorScorecard
    {
        return VendorScorecard::firstOrNew([
            'vendor_id'    => $vendorId,
            'company_id'   => $companyId,
            'period_year'  => $year,
            'period_month' => $month,
        ]);
    }
}
<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementInvoiceMatch extends Model
{
    use HasFactory;

    protected $table = 'procurement_invoice_matches';

    protected $fillable = [
        'company_id',
        'purchase_order_id',
        'goods_receipt_id',
        'finance_invoice_id',
        'po_total',
        'grn_total',
        'invoice_total',
        'po_grn_variance',
        'grn_invoice_variance',
        'tolerance_percent',
        'status',
        'notes',
        'matched_at',
    ];

    protected $casts = [
        'po_total'              => 'decimal:2',
        'grn_total'             => 'decimal:2',
        'invoice_total'         => 'decimal:2',
        'po_grn_variance'       => 'decimal:2',
        'grn_invoice_variance'  => 'decimal:2',
        'tolerance_percent'     => 'decimal:2',
        'matched_at'            => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function isMatched(): bool
    {
        $poTotal = (float) $this->po_total;
        $grnTotal = (float) $this->grn_total;
        $tolerancePct = (float) $this->tolerance_percent;

        $poGrnOk = max($poTotal, 0.01) > 0
            && (abs($poTotal - $grnTotal) / max($poTotal, 0.01)) * 100 <= $tolerancePct;

        if (! $poGrnOk) {
            return false;
        }

        if ($this->invoice_total !== null) {
            $grnInvVariance = (float) $this->grn_invoice_variance;
            return (abs($grnInvVariance) / max($grnTotal, 0.01)) * 100 <= $tolerancePct;
        }

        return true;
    }

    public function checkMatch(): string
    {
        $poTotal = (float) $this->po_total;
        $grnTotal = (float) $this->grn_total;
        $tolerancePct = (float) $this->tolerance_percent;

        $poGrnVariancePct = max($poTotal, 0.01) > 0
            ? (abs($poTotal - $grnTotal) / max($poTotal, 0.01)) * 100
            : 0;

        if ($poGrnVariancePct > $tolerancePct) {
            return 'po_grn_mismatch';
        }

        if ($this->invoice_total !== null) {
            $grnInvVariancePct = max($grnTotal, 0.01) > 0
                ? (abs($grnTotal - (float) $this->invoice_total) / max($grnTotal, 0.01)) * 100
                : 0;

            if ($grnInvVariancePct > $tolerancePct) {
                return 'grn_invoice_mismatch';
            }

            return 'matched';
        }

        return 'pending_invoice';
    }
}
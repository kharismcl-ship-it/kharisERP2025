<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPerformanceRecord extends Model
{
    use HasFactory;

    protected $table = 'procurement_vendor_performance_records';

    protected $fillable = [
        'company_id',
        'vendor_id',
        'purchase_order_id',
        'goods_receipt_id',
        'expected_delivery_date',
        'actual_delivery_date',
        'days_late',
        'quantity_ordered',
        'quantity_received',
        'quantity_rejected',
        'quality_rate',
        'po_unit_price',
        'grn_unit_price',
        'price_variance_pct',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'actual_delivery_date'   => 'date',
        'days_late'              => 'integer',
        'quantity_ordered'       => 'decimal:4',
        'quantity_received'      => 'decimal:4',
        'quantity_rejected'      => 'decimal:4',
        'quality_rate'           => 'decimal:2',
        'po_unit_price'          => 'decimal:4',
        'grn_unit_price'         => 'decimal:4',
        'price_variance_pct'     => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }
}
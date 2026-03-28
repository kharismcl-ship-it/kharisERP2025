<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionLotLine extends Model
{
    use HasFactory;

    protected $table = 'procurement_inspection_lot_lines';

    protected $fillable = [
        'lot_id',
        'goods_receipt_line_id',
        'item_id',
        'quantity_inspected',
        'quantity_passed',
        'quantity_failed',
        'defect_type',
        'defect_description',
        'disposition',
    ];

    protected $casts = [
        'quantity_inspected' => 'decimal:4',
        'quantity_passed'    => 'decimal:4',
        'quantity_failed'    => 'decimal:4',
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(InspectionLot::class);
    }

    public function goodsReceiptLine(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptLine::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
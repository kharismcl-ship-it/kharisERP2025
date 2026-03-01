<?php

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderLine extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_lines';

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'description',
        'quantity',
        'unit_of_measure',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'line_total',
        'quantity_received',
    ];

    protected $casts = [
        'quantity'          => 'decimal:4',
        'unit_price'        => 'decimal:4',
        'tax_rate'          => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'line_total'        => 'decimal:2',
        'quantity_received' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $line) {
            $line->tax_amount = round(($line->quantity * $line->unit_price) * ($line->tax_rate / 100), 2);
            $line->line_total = round($line->quantity * $line->unit_price + $line->tax_amount, 2);
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function goodsReceiptLines(): HasMany
    {
        return $this->hasMany(GoodsReceiptLine::class);
    }

    public function getRemainingQuantityAttribute(): float
    {
        return max(0, (float) $this->quantity - (float) $this->quantity_received);
    }

    public function isFullyReceived(): bool
    {
        return (float) $this->quantity_received >= (float) $this->quantity;
    }
}
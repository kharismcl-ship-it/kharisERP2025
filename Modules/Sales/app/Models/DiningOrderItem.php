<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningOrderItem extends Model
{
    protected $fillable = [
        'dining_order_id',
        'catalog_item_id',
        'quantity',
        'unit_price',
        'line_total',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_price' => 'decimal:4',
        'line_total' => 'decimal:2',
    ];

    const STATUSES = ['pending', 'in_prep', 'ready', 'served', 'cancelled'];

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->line_total = round($item->quantity * $item->unit_price, 2);
        });
    }

    public function diningOrder(): BelongsTo
    {
        return $this->belongsTo(DiningOrder::class, 'dining_order_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(SalesCatalog::class, 'catalog_item_id');
    }
}
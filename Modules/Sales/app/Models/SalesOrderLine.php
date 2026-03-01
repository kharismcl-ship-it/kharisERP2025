<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderLine extends Model
{
    protected $fillable = [
        'order_id',
        'catalog_item_id',
        'quantity',
        'unit_price',
        'discount_pct',
        'line_total',
        'fulfilled_quantity',
        'fulfillment_status',
    ];

    protected $casts = [
        'quantity'           => 'decimal:3',
        'unit_price'         => 'decimal:4',
        'discount_pct'       => 'decimal:2',
        'line_total'         => 'decimal:2',
        'fulfilled_quantity' => 'decimal:3',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $line) {
            $line->line_total = round(
                $line->quantity * $line->unit_price * (1 - ($line->discount_pct / 100)),
                2
            );

            $line->fulfillment_status = match (true) {
                $line->fulfilled_quantity <= 0              => 'pending',
                $line->fulfilled_quantity < $line->quantity => 'partial',
                default                                     => 'fulfilled',
            };
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(SalesCatalog::class, 'catalog_item_id');
    }
}
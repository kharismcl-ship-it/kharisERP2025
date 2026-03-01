<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSaleLine extends Model
{
    protected $fillable = [
        'pos_sale_id',
        'catalog_item_id',
        'quantity',
        'unit_price',
        'discount_pct',
        'line_total',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'unit_price'   => 'decimal:4',
        'discount_pct' => 'decimal:2',
        'line_total'   => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $line) {
            $line->line_total = round(
                $line->quantity * $line->unit_price * (1 - ($line->discount_pct / 100)),
                2
            );
        });
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(SalesCatalog::class, 'catalog_item_id');
    }
}
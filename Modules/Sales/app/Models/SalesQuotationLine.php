<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesQuotationLine extends Model
{
    protected $fillable = [
        'quotation_id',
        'catalog_item_id',
        'quantity',
        'unit_price',
        'discount_pct',
        'line_total',
        'notes',
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

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(SalesQuotation::class, 'quotation_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(SalesCatalog::class, 'catalog_item_id');
    }
}
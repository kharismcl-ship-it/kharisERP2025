<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOpportunityItem extends Model
{
    protected $fillable = [
        'opportunity_id',
        'catalog_item_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_price' => 'decimal:4',
    ];

    public function getLineTotalAttribute(): float
    {
        return round($this->quantity * $this->unit_price, 2);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(SalesOpportunity::class, 'opportunity_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(SalesCatalog::class, 'catalog_item_id');
    }
}
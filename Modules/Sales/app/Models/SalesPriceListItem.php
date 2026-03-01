<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesPriceListItem extends Model
{
    protected $fillable = [
        'price_list_id',
        'catalog_item_id',
        'override_price',
        'min_quantity',
    ];

    protected $casts = [
        'override_price' => 'decimal:4',
        'min_quantity'   => 'decimal:3',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(SalesPriceList::class, 'price_list_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(SalesCatalog::class, 'catalog_item_id');
    }
}
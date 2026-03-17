<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmOrderItem extends Model
{
    protected $table = 'farm_order_items';

    protected $fillable = [
        'farm_order_id',
        'farm_produce_inventory_id',
        'product_name',
        'unit',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(FarmOrder::class, 'farm_order_id');
    }

    public function produceInventory(): BelongsTo
    {
        return $this->belongsTo(FarmProduceInventory::class, 'farm_produce_inventory_id');
    }
}

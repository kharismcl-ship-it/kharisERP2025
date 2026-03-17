<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmBundleItem extends Model
{
    protected $table = 'farm_bundle_items';

    public $timestamps = false;

    protected $fillable = [
        'farm_bundle_id', 'farm_produce_inventory_id', 'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(FarmBundle::class, 'farm_bundle_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(FarmProduceInventory::class, 'farm_produce_inventory_id');
    }
}

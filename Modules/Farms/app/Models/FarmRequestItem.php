<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmRequestItem extends Model
{
    protected $table = 'farm_request_items';

    protected $fillable = [
        'farm_request_id',
        'item_id',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_cost'  => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            if ($item->quantity !== null && $item->unit_cost !== null) {
                $item->total_cost = round((float) $item->quantity * (float) $item->unit_cost, 2);
            }
        });
    }

    public function farmRequest(): BelongsTo
    {
        return $this->belongsTo(FarmRequest::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(\Modules\ProcurementInventory\Models\Item::class, 'item_id');
    }
}

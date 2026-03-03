<?php

namespace Modules\Construction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorRequestItem extends Model
{
    protected $fillable = [
        'contractor_request_id',
        'item_id',
        'material_name',
        'unit',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_cost'  => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        $calcTotal = function (self $item) {
            if ($item->unit_cost !== null && $item->quantity !== null) {
                $item->total_cost = round((float) $item->unit_cost * (float) $item->quantity, 2);
            }
        };

        static::creating($calcTotal);
        static::updating($calcTotal);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(ContractorRequest::class, 'contractor_request_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(\Modules\ProcurementInventory\Models\Item::class);
    }
}

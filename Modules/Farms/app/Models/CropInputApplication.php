<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use Modules\ProcurementInventory\Models\Item;

class CropInputApplication extends Model
{
    protected $fillable = [
        'crop_cycle_id',
        'farm_id',
        'company_id',
        'item_id',
        'application_date',
        'input_type',
        'product_name',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'application_method',
        'notes',
    ];

    protected $casts = [
        'application_date' => 'date',
        'quantity'         => 'decimal:4',
        'unit_cost'        => 'decimal:4',
        'total_cost'       => 'decimal:2',
    ];

    const INPUT_TYPES = ['seed', 'fertilizer', 'pesticide', 'herbicide', 'irrigation_water', 'other'];

    protected static function booted(): void
    {
        static::saving(function (self $record) {
            if ($record->quantity && $record->unit_cost && ! $record->isDirty('total_cost')) {
                $record->total_cost = round($record->quantity * $record->unit_cost, 2);
            }
        });
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Optional link to a ProcurementInventory item (seed, fertilizer, pesticide, etc.)
     * Allows stock tracking and cost pull-through from the inventory module.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

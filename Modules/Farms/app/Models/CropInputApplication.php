<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use Modules\ProcurementInventory\Models\Item;
use App\Models\Concerns\BelongsToCompany;

class CropInputApplication extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'crop_cycle_id',
        'farm_id',
        'farm_plot_id',
        'company_id',
        'item_id',
        'farm_input_chemical_id',
        'applicator_worker_id',
        'application_date',
        'input_type',
        'product_name',
        'quantity',
        'unit',
        'quantity_used',
        'unit_cost',
        'total_cost',
        'application_method',
        'weather_condition',
        'wind_speed_kmh',
        'temperature_c',
        'humidity_pct',
        'phi_compliant',
        'notes',
    ];

    protected $casts = [
        'application_date' => 'date',
        'quantity'         => 'decimal:4',
        'unit_cost'        => 'decimal:4',
        'total_cost'       => 'decimal:2',
        'wind_speed_kmh'   => 'float',
        'temperature_c'    => 'float',
        'humidity_pct'     => 'float',
        'phi_compliant'    => 'boolean',
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

    public function farmPlot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class, 'farm_plot_id');
    }

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(FarmInputChemical::class, 'farm_input_chemical_id');
    }

    public function applicatorWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class, 'applicator_worker_id');
    }

    /**
     * Alias: quantity_used falls back to quantity for backwards compatibility.
     */
    public function getQuantityUsedAttribute(): mixed
    {
        return $this->attributes['quantity_used'] ?? $this->attributes['quantity'] ?? null;
    }
}

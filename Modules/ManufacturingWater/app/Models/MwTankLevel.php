<?php

namespace Modules\ManufacturingWater\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

class MwTankLevel extends Model
{
    use BelongsToCompany;

    protected $table = 'mw_tank_levels';

    protected $fillable = [
        'plant_id',
        'company_id',
        'tank_name',
        'capacity_liters',
        'current_level_liters',
        'recorded_at',
        'notes',
    ];

    protected $casts = [
        'capacity_liters'      => 'decimal:2',
        'current_level_liters' => 'decimal:2',
        'recorded_at'          => 'datetime',
    ];

    public function getFillPercentageAttribute(): float
    {
        if (! $this->capacity_liters || $this->capacity_liters == 0) {
            return 0;
        }
        return round(($this->current_level_liters / $this->capacity_liters) * 100, 1);
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(MwPlant::class, 'plant_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
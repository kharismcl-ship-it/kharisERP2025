<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class FarmPlot extends Model
{
    protected $fillable = [
        'farm_id',
        'company_id',
        'name',
        'description',
        'area',
        'area_unit',
        'soil_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'area' => 'decimal:4',
    ];

    const SOIL_TYPES = ['clay', 'sandy', 'loam', 'silty', 'peaty', 'chalky'];
    const STATUSES   = ['active', 'fallow', 'preparing'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function cropCycles(): HasMany
    {
        return $this->hasMany(CropCycle::class);
    }
}

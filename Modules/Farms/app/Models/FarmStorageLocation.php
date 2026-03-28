<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmStorageLocation extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_storage_locations';

    protected $fillable = [
        'company_id',
        'farm_id',
        'name',
        'type',
        'capacity_tonnes',
        'current_stock_tonnes',
        'temperature_c',
        'humidity_pct',
        'last_checked_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'capacity_tonnes'      => 'float',
        'current_stock_tonnes' => 'float',
        'temperature_c'        => 'float',
        'humidity_pct'         => 'float',
        'last_checked_at'      => 'datetime',
        'is_active'            => 'boolean',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function postHarvestRecords(): HasMany
    {
        return $this->hasMany(FarmPostHarvestRecord::class, 'farm_storage_location_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
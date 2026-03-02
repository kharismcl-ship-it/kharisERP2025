<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class Farm extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'location',
        'total_area',
        'area_unit',
        'type',
        'owner_name',
        'owner_phone',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_area' => 'decimal:4',
    ];

    const TYPES    = ['crop', 'livestock', 'mixed', 'aquaculture'];
    const STATUSES = ['active', 'inactive', 'fallow'];

    protected static function booted(): void
    {
        static::creating(function (self $farm) {
            if (empty($farm->slug)) {
                $farm->slug = Str::slug($farm->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function plots(): HasMany
    {
        return $this->hasMany(FarmPlot::class);
    }

    public function cropCycles(): HasMany
    {
        return $this->hasMany(CropCycle::class);
    }

    public function livestockBatches(): HasMany
    {
        return $this->hasMany(LivestockBatch::class);
    }

    public function harvestRecords(): HasMany
    {
        return $this->hasMany(HarvestRecord::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(FarmExpense::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(FarmEquipment::class);
    }

    public function weatherLogs(): HasMany
    {
        return $this->hasMany(FarmWeatherLog::class);
    }

    public function soilTestRecords(): HasMany
    {
        return $this->hasMany(SoilTestRecord::class);
    }

    public function produceInventories(): HasMany
    {
        return $this->hasMany(FarmProduceInventory::class);
    }
}

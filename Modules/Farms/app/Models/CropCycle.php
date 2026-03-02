<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use Modules\Farms\Events\CropCycleStarted;
use Modules\Farms\Models\CropActivity;
use Modules\Farms\Models\CropInputApplication;
use Modules\Farms\Models\CropScoutingRecord;
use App\Models\Concerns\BelongsToCompany;

class CropCycle extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'farm_id',
        'farm_plot_id',
        'company_id',
        'crop_name',
        'variety',
        'season',
        'planting_date',
        'expected_harvest_date',
        'actual_harvest_date',
        'planted_area',
        'planted_area_unit',
        'status',
        'expected_yield',
        'yield_unit',
        'notes',
    ];

    protected $casts = [
        'planting_date'          => 'date',
        'expected_harvest_date'  => 'date',
        'actual_harvest_date'    => 'date',
        'planted_area'           => 'decimal:4',
        'expected_yield'         => 'decimal:3',
    ];

    const STATUSES = ['preparing', 'growing', 'harvested', 'failed'];

    protected static function booted(): void
    {
        static::updated(function (self $cycle) {
            if ($cycle->wasChanged('status') && $cycle->status === 'growing') {
                CropCycleStarted::dispatch($cycle);
            }
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class, 'farm_plot_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function harvestRecords(): HasMany
    {
        return $this->hasMany(HarvestRecord::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(FarmExpense::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CropActivity::class);
    }

    public function inputApplications(): HasMany
    {
        return $this->hasMany(CropInputApplication::class);
    }

    public function scoutingRecords(): HasMany
    {
        return $this->hasMany(CropScoutingRecord::class);
    }

    public function getTotalInputCostAttribute(): float
    {
        return (float) $this->inputApplications()->sum('total_cost');
    }

    public function getTotalActivityCostAttribute(): float
    {
        return (float) $this->activities()->sum('cost');
    }

    public function getTotalHarvestedAttribute(): float
    {
        return (float) $this->harvestRecords()->sum('quantity');
    }

    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->harvestRecords()->sum('total_revenue');
    }
}

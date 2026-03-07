<?php

namespace Modules\ManufacturingWater\Models;

use App\Models\Company;
use EduardoRibeiroDev\FilamentLeaflet\Concerns\HasGeoJsonFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToCompany;

class MwPlant extends Model
{
    use BelongsToCompany, HasGeoJsonFile;

    protected $table = 'mw_plants';

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'location',
        'type',
        'source_type',
        'capacity_liters_per_day',
        'status',
        'description',
        'latitude',
        'longitude',
        'geometry',
    ];

    protected $casts = [
        'capacity_liters_per_day' => 'decimal:2',
        'latitude'                => 'float',
        'longitude'               => 'float',
        'geometry'                => 'array',
    ];

    public function getGeoJsonFileAttributeName(): string { return 'geometry'; }

    public function getGeoJsonUrl(): ?string
    {
        if (empty($this->geometry)) { return null; }
        $json = is_array($this->geometry) ? json_encode($this->geometry) : $this->geometry;
        return 'data:application/json;base64,' . base64_encode($json);
    }

    const TYPES        = ['treatment', 'bottling', 'distribution'];
    const SOURCE_TYPES = ['borehole', 'river', 'reservoir', 'municipal', 'spring'];
    const STATUSES     = ['active', 'idle', 'maintenance', 'decommissioned'];

    protected static function booted(): void
    {
        static::creating(function (self $plant) {
            if (empty($plant->slug)) {
                $plant->slug = Str::slug($plant->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function treatmentStages(): HasMany
    {
        return $this->hasMany(MwTreatmentStage::class, 'plant_id')->orderBy('stage_order');
    }

    public function waterTestRecords(): HasMany
    {
        return $this->hasMany(MwWaterTestRecord::class, 'plant_id');
    }

    public function tankLevels(): HasMany
    {
        return $this->hasMany(MwTankLevel::class, 'plant_id');
    }

    public function distributionRecords(): HasMany
    {
        return $this->hasMany(MwDistributionRecord::class, 'plant_id');
    }

    public function chemicalUsages(): HasMany
    {
        return $this->hasMany(MwChemicalUsage::class, 'plant_id');
    }
}

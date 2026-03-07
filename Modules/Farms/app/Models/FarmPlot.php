<?php

namespace Modules\Farms\Models;

use EduardoRibeiroDev\FilamentLeaflet\Concerns\HasGeoJsonFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmPlot extends Model
{
    use BelongsToCompany, HasGeoJsonFile;

    protected $fillable = [
        'farm_id',
        'company_id',
        'name',
        'description',
        'area',
        'area_unit',
        'soil_type',
        'location',
        'latitude',
        'longitude',
        'status',
        'notes',
        'geometry',
    ];

    protected $casts = [
        'area'      => 'decimal:4',
        'latitude'  => 'float',
        'longitude' => 'float',
        'geometry'  => 'array',
    ];

    const SOIL_TYPES = ['clay', 'sandy', 'loam', 'silty', 'peaty', 'chalky'];
    const STATUSES   = ['active', 'fallow', 'preparing'];

    public function getGeoJsonFileAttributeName(): string { return 'geometry'; }

    public function getGeoJsonUrl(): ?string
    {
        if (empty($this->geometry)) { return null; }
        $json = is_array($this->geometry) ? json_encode($this->geometry) : $this->geometry;
        return 'data:application/json;base64,' . base64_encode($json);
    }

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

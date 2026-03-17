<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use EduardoRibeiroDev\FilamentLeaflet\Concerns\HasGeoJsonFile;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class Farm extends Model
{
    use BelongsToCompany, HasGeoJsonFile;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'location',
        'latitude',
        'longitude',
        'total_area',
        'area_unit',
        'type',
        'owner_name',
        'owner_phone',
        'status',
        'notes',
        'geometry',
        'about',
        'cover_image',
        'gallery_images',
        'video_url',
        'established_year',
    ];

    protected $casts = [
        'total_area'       => 'decimal:4',
        'latitude'         => 'float',
        'longitude'        => 'float',
        'geometry'         => 'array',
        'gallery_images'   => 'array',
        'established_year' => 'integer',
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

    public function getGeoJsonFileAttributeName(): string { return 'geometry'; }

    public function getGeoJsonUrl(): ?string
    {
        if (empty($this->geometry)) { return null; }
        $json = is_array($this->geometry) ? json_encode($this->geometry) : $this->geometry;
        return 'data:application/json;base64,' . base64_encode($json);
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

    public function workers(): HasMany
    {
        return $this->hasMany(FarmWorker::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(FarmWorkerAttendance::class);
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(FarmDailyReport::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(FarmDocument::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(FarmRequest::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(FarmSeason::class);
    }
}

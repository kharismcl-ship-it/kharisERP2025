<?php

namespace Modules\ManufacturingPaper\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MpPlant extends Model
{
    protected $table = 'mp_plants';

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'location',
        'type',
        'capacity',
        'capacity_unit',
        'status',
        'description',
    ];

    const TYPES = ['integrated', 'pulp_only', 'paper_only', 'recycled'];
    const STATUSES = ['active', 'idle', 'maintenance', 'decommissioned'];

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

    public function productionLines(): HasMany
    {
        return $this->hasMany(MpProductionLine::class, 'plant_id');
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(MpProductionBatch::class, 'plant_id');
    }

    public function equipmentLogs(): HasMany
    {
        return $this->hasMany(MpEquipmentLog::class, 'plant_id');
    }
}
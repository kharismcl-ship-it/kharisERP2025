<?php

namespace Modules\ManufacturingPaper\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MpProductionLine extends Model
{
    protected $table = 'mp_production_lines';

    protected $fillable = [
        'plant_id',
        'company_id',
        'name',
        'line_type',
        'capacity_per_day',
        'capacity_unit',
        'is_active',
        'status',
        'notes',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'capacity_per_day' => 'decimal:2',
    ];

    const LINE_TYPES = ['paper', 'pulp', 'coating', 'finishing'];
    const STATUSES   = ['operational', 'maintenance', 'idle', 'decommissioned'];

    public function plant(): BelongsTo
    {
        return $this->belongsTo(MpPlant::class, 'plant_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(MpProductionBatch::class, 'production_line_id');
    }

    public function equipmentLogs(): HasMany
    {
        return $this->hasMany(MpEquipmentLog::class, 'production_line_id');
    }
}
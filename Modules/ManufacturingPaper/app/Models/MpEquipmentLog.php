<?php

namespace Modules\ManufacturingPaper\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpEquipmentLog extends Model
{
    protected $table = 'mp_equipment_logs';

    protected $fillable = [
        'plant_id',
        'production_line_id',
        'company_id',
        'equipment_name',
        'log_type',
        'description',
        'logged_at',
        'resolved_at',
        'cost',
        'status',
    ];

    protected $casts = [
        'logged_at'   => 'datetime',
        'resolved_at' => 'datetime',
        'cost'        => 'decimal:2',
    ];

    const LOG_TYPES = ['maintenance', 'breakdown', 'inspection', 'upgrade', 'calibration'];
    const STATUSES  = ['open', 'in_progress', 'resolved', 'closed'];

    public function getIsResolvedAttribute(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(MpPlant::class, 'plant_id');
    }

    public function productionLine(): BelongsTo
    {
        return $this->belongsTo(MpProductionLine::class, 'production_line_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
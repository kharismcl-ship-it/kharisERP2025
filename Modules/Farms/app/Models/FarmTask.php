<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmTask extends Model
{
    protected $table = 'farm_tasks';

    protected $fillable = [
        'farm_id', 'farm_plot_id', 'crop_cycle_id', 'livestock_batch_id',
        'assigned_to_worker_id', 'farm_equipment_id', 'vehicle_id', 'company_id',
        'title', 'description', 'task_type', 'priority',
        'due_date', 'completed_at', 'notes',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    const TASK_TYPES = [
        'weeding', 'spraying', 'harvesting', 'irrigation',
        'maintenance', 'feeding', 'health_check', 'planting',
        'soil_prep', 'scouting', 'other',
    ];

    const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class, 'farm_plot_id');
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function livestockBatch(): BelongsTo
    {
        return $this->belongsTo(LivestockBatch::class);
    }

    public function assignedWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class, 'assigned_to_worker_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Farm-owned equipment assigned for this task (from FarmEquipment register).
     */
    public function farmEquipment(): BelongsTo
    {
        return $this->belongsTo(FarmEquipment::class, 'farm_equipment_id');
    }

    /**
     * Optional Fleet vehicle assigned for transport tasks.
     * Null when Fleet module not installed or not applicable.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(\Modules\Fleet\Models\Vehicle::class, 'vehicle_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        return ! $this->completed_at && $this->due_date && now()->gt($this->due_date);
    }
}
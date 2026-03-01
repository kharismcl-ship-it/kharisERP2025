<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmWorker extends Model
{
    protected $table = 'farm_workers';

    protected $fillable = [
        'farm_id', 'employee_id', 'company_id',
        'name', 'role', 'daily_rate', 'is_active', 'notes',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    const ROLES = [
        'labourer', 'supervisor', 'tractor_operator',
        'irrigation_specialist', 'vet_assistant', 'guard', 'other',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(\Modules\HR\Models\Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(FarmTask::class, 'assigned_to_worker_id');
    }

    /**
     * Display name: use HR employee name if linked, otherwise local name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->employee?->full_name ?? $this->name ?? 'Worker #' . $this->id;
    }
}
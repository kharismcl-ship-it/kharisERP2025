<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class FarmWorker extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_workers';

    protected $fillable = [
        'farm_id', 'employee_id', 'company_id',
        'name', 'role', 'worker_type', 'contract_start', 'contract_end',
        'daily_rate', 'is_active', 'notes',
    ];

    protected $casts = [
        'daily_rate'     => 'decimal:2',
        'is_active'      => 'boolean',
        'contract_start' => 'date',
        'contract_end'   => 'date',
    ];

    const ROLES = [
        'manager', 'supervisor', 'labourer', 'tractor_operator',
        'irrigation_specialist', 'vet_assistant', 'guard', 'other',
    ];

    const WORKER_TYPES = ['permanent', 'daily', 'contract'];

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

    public function attendances(): HasMany
    {
        return $this->hasMany(FarmWorkerAttendance::class);
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(FarmDailyReport::class);
    }

    /**
     * Display name: use HR employee name if linked, otherwise local name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->employee?->full_name ?? $this->name ?? 'Worker #' . $this->id;
    }
}
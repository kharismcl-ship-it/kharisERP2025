<?php

namespace Modules\Construction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Company;

class ConstructionProject extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'location',
        'client_name',
        'client_contact',
        'project_manager',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'contract_value',
        'budget',
        'total_spent',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'expected_end_date' => 'date',
        'actual_end_date'   => 'date',
        'contract_value'    => 'decimal:2',
        'budget'            => 'decimal:2',
        'total_spent'       => 'decimal:2',
    ];

    const STATUSES = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];

    protected static function booted(): void
    {
        static::creating(function (self $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class)->orderBy('order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(ProjectBudgetItem::class);
    }

    public function materialUsages(): HasMany
    {
        return $this->hasMany(MaterialUsage::class);
    }

    public function getBudgetVarianceAttribute(): float
    {
        return (float) $this->budget - (float) $this->total_spent;
    }

    public function getOverallProgressAttribute(): int
    {
        $phases = $this->phases;
        if ($phases->isEmpty()) {
            return 0;
        }
        return (int) $phases->avg('progress_percent');
    }
}

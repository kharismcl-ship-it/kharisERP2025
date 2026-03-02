<?php

namespace Modules\Construction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use Modules\Construction\Events\ProjectMilestoneCompleted;
use Modules\Construction\Events\ProjectPhaseApproved;
use App\Models\Concerns\BelongsToCompany;

class ProjectPhase extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'construction_project_id',
        'company_id',
        'name',
        'description',
        'order',
        'planned_start',
        'planned_end',
        'actual_start',
        'actual_end',
        'budget',
        'spent',
        'progress_percent',
        'status',
    ];

    protected $casts = [
        'planned_start'    => 'date',
        'planned_end'      => 'date',
        'actual_start'     => 'date',
        'actual_end'       => 'date',
        'budget'           => 'decimal:2',
        'spent'            => 'decimal:2',
        'progress_percent' => 'integer',
    ];

    const STATUSES = ['pending', 'in_progress', 'completed', 'on_hold'];

    protected static function booted(): void
    {
        static::updated(function (self $phase) {
            if ($phase->isDirty('status')) {
                if ($phase->status === 'in_progress') {
                    ProjectPhaseApproved::dispatch($phase);
                }

                if ($phase->status === 'completed') {
                    $project = $phase->project;
                    if ($project) {
                        ProjectMilestoneCompleted::dispatch($project, $phase);
                    }
                }
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function materialUsages(): HasMany
    {
        return $this->hasMany(MaterialUsage::class);
    }
}
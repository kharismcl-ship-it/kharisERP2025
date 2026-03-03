<?php

namespace Modules\Construction\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerTaskAssignment extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'construction_worker_id',
        'project_task_id',
        'construction_project_id',
        'role',
        'assigned_from',
        'assigned_to',
    ];

    protected $casts = [
        'assigned_from' => 'date',
        'assigned_to'   => 'date',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(ConstructionWorker::class, 'construction_worker_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectTask::class, 'project_task_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }
}

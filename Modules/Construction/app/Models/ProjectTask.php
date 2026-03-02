<?php

namespace Modules\Construction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class ProjectTask extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'project_phase_id',
        'construction_project_id',
        'company_id',
        'contractor_id',
        'name',
        'description',
        'due_date',
        'completed_at',
        'status',
        'priority',
        'notes',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'date',
        'priority'     => 'integer',
    ];

    const STATUSES = ['pending', 'in_progress', 'completed', 'blocked'];
    const PRIORITIES = [1 => 'Low', 2 => 'Medium', 3 => 'High'];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }
}

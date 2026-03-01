<?php

namespace Modules\Construction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class ProjectBudgetItem extends Model
{
    protected $fillable = [
        'construction_project_id',
        'company_id',
        'category',
        'description',
        'budgeted_amount',
        'actual_amount',
        'notes',
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'actual_amount'   => 'decimal:2',
    ];

    const CATEGORIES = ['labour', 'materials', 'equipment', 'subcontractor', 'overhead', 'other'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getVarianceAttribute(): float
    {
        return (float) $this->budgeted_amount - (float) $this->actual_amount;
    }
}

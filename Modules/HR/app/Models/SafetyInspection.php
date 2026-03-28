<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class SafetyInspection extends Model
{
    protected $table = 'hr_safety_inspections';

    protected $fillable = [
        'company_id',
        'title',
        'location',
        'inspection_date',
        'inspected_by_employee_id',
        'checklist_items',
        'overall_passed',
        'summary_notes',
        'follow_up_required',
        'follow_up_date',
        'status',
    ];

    protected $casts = [
        'inspection_date'   => 'date',
        'follow_up_date'    => 'date',
        'checklist_items'   => 'array',
        'overall_passed'    => 'boolean',
        'follow_up_required'=> 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'inspected_by_employee_id');
    }
}
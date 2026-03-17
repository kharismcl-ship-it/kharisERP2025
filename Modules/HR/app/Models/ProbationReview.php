<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProbationReview extends Model
{
    protected $table = 'hr_probation_reviews';

    protected $fillable = [
        'employee_id', 'company_id', 'reviewer_employee_id',
        'probation_start_date', 'probation_end_date', 'review_date',
        'status', 'probation_months', 'extension_months', 'extended_end_date',
        'performance_summary', 'strengths', 'areas_for_improvement',
        'reviewer_recommendation', 'hr_decision_notes', 'overall_rating',
        'employee_notified', 'notified_at', 'created_by',
    ];

    protected $casts = [
        'probation_start_date' => 'date',
        'probation_end_date'   => 'date',
        'extended_end_date'    => 'date',
        'review_date'          => 'date',
        'notified_at'          => 'datetime',
        'employee_notified'    => 'boolean',
    ];

    const STATUSES = [
        'pending'   => 'Pending Review',
        'in_review' => 'In Review',
        'passed'    => 'Passed',
        'extended'  => 'Extended',
        'failed'    => 'Failed',
    ];

    const RECOMMENDATIONS = [
        'confirm'    => 'Confirm Employment',
        'extend'     => 'Extend Probation',
        'terminate'  => 'Terminate Employment',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending' && $this->probation_end_date?->isPast();
    }

    public function getEffectiveEndDateAttribute(): \Carbon\Carbon
    {
        return $this->extended_end_date ?? $this->probation_end_date;
    }
}

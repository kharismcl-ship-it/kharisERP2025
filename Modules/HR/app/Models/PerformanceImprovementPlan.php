<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceImprovementPlan extends Model
{
    protected $table = 'hr_performance_improvement_plans';

    protected $fillable = [
        'reference', 'employee_id', 'company_id', 'manager_employee_id',
        'hr_officer_id', 'start_date', 'end_date', 'review_date',
        'status', 'performance_issue', 'improvement_goals',
        'support_provided', 'milestones', 'progress_notes',
        'outcome', 'outcome_notes',
        'employee_acknowledged', 'acknowledged_at', 'created_by',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'end_date'        => 'date',
        'review_date'     => 'date',
        'acknowledged_at' => 'datetime',
        'employee_acknowledged' => 'boolean',
    ];

    const STATUSES = [
        'draft'     => 'Draft',
        'active'    => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'escalated' => 'Escalated',
    ];

    const OUTCOMES = [
        'successful'   => 'Successful',
        'unsuccessful' => 'Unsuccessful',
        'extended'     => 'Extended',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (! $model->reference) {
                $prefix = 'PIP-' . now()->format('Ym') . '-';
                $last   = self::where('reference', 'like', $prefix . '%')
                    ->orderByDesc('reference')
                    ->value('reference');
                $seq    = $last ? (int) substr($last, -5) + 1 : 1;
                $model->reference = $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_employee_id');
    }

    public function hrOfficer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'hr_officer_id');
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
        return $this->status === 'active' && $this->end_date?->isPast();
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->status !== 'active') {
            return null;
        }
        return max(0, (int) now()->diffInDays($this->end_date, false));
    }
}

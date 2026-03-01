<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeGoal extends Model
{
    protected $table = 'hr_employee_goals';

    protected $fillable = [
        'employee_id', 'performance_cycle_id', 'title', 'description',
        'target_value', 'actual_value', 'unit_of_measure', 'due_date',
        'status', 'priority', 'notes',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'due_date'     => 'date',
    ];

    const STATUSES   = ['not_started' => 'Not Started', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
    const PRIORITIES = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];

    public function getCompletionPercentageAttribute(): float
    {
        if (! $this->target_value || $this->target_value == 0) {
            return 0;
        }
        return round(($this->actual_value / $this->target_value) * 100, 1);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function performanceCycle(): BelongsTo
    {
        return $this->belongsTo(PerformanceCycle::class);
    }
}
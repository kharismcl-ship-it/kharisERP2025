<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveApprovalLevel extends Model
{
    use HasFactory;

    protected $table = 'hr_leave_approval_levels';

    protected $fillable = [
        'workflow_id',
        'level_number',
        'approver_type',
        'approver_employee_id',
        'approver_department_id',
        'approver_role',
        'is_required',
        'approval_order',
    ];

    protected $casts = [
        'level_number' => 'integer',
        'is_required' => 'boolean',
        'approval_order' => 'integer',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(LeaveApprovalWorkflow::class, 'workflow_id');
    }

    public function approverEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function approverDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'approver_department_id');
    }

    public function approvalRequests(): HasMany
    {
        return $this->hasMany(LeaveApprovalRequest::class, 'approval_level_id');
    }

    public function getApproverForEmployee(Employee $employee): ?Employee
    {
        switch ($this->approver_type) {
            case 'manager':
                return $employee->manager;
            case 'specific_employee':
                return $this->approverEmployee;
            case 'department_head':
                return $this->approverDepartment->head;
            case 'hr':
                return Employee::whereHas('jobPosition', function ($query) {
                    $query->where('title', 'like', '%HR%')
                        ->orWhere('title', 'like', '%Human Resources%');
                })->first();
            default:
                return null;
        }
    }
}

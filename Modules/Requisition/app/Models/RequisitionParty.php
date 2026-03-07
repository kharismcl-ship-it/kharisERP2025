<?php

namespace Modules\Requisition\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;

class RequisitionParty extends Model
{
    protected $fillable = [
        'requisition_id',
        'party_type',
        'employee_id',
        'department_id',
        'reason',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
        ];
    }

    const REASONS = [
        'for_info'     => 'For Information',
        'for_action'   => 'For Action',
        'for_approval' => 'For Approval',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Resolve all employees who should be notified for this party record.
     * If party_type = employee, returns [employee].
     * If party_type = department, returns all employees in that department.
     *
     * @return \Illuminate\Support\Collection<Employee>
     */
    public function resolveRecipients(): \Illuminate\Support\Collection
    {
        if ($this->party_type === 'employee' && $this->employee) {
            return collect([$this->employee]);
        }

        if ($this->party_type === 'department' && $this->department) {
            return Employee::withoutGlobalScopes()
                ->where('department_id', $this->department_id)
                ->get();
        }

        return collect();
    }
}
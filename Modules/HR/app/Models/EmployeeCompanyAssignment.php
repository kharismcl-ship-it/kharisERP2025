<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Events\EmployeeCompanyAssignmentCreated;
use Modules\HR\Events\EmployeeCompanyAssignmentUpdated;

class EmployeeCompanyAssignment extends Model
{
    protected $fillable = [
        'employee_id',
        'company_id',
        'start_date',
        'end_date',
        'assignment_reason',
        'role',
        'assigned_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'assigned_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => EmployeeCompanyAssignmentCreated::class,
        'updated' => EmployeeCompanyAssignmentUpdated::class,
    ];

    /**
     * Get the employee for this assignment.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the company for this assignment.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

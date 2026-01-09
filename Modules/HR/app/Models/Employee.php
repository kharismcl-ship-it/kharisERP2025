<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_employees';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'other_names',
        'full_name',
        'gender',
        'dob',
        'phone',
        'alt_phone',
        'email',
        'national_id_number',
        'marital_status',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'department_id',
        'job_position_id',
        'hire_date',
        'employment_type',
        'employment_status',
        'reporting_to_employee_id',
        'photo_path',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'dob' => 'date',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            $employee->full_name = $employee->first_name.' '.$employee->last_name;
        });

        static::updating(function ($employee) {
            $employee->full_name = $employee->first_name.' '.$employee->last_name;
        });
    }

    /**
     * Get the company that owns the employee.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user associated with the employee.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the department the employee belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the job position of the employee.
     */
    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class);
    }

    /**
     * Get the manager of the employee.
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'reporting_to_employee_id');
    }

    /**
     * Get the subordinates of the employee.
     */
    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'reporting_to_employee_id');
    }

    /**
     * Get the hostel staff assignments for the employee.
     */
    public function hostelStaffAssignments()
    {
        return $this->hasMany(HostelStaffAssignment::class, 'employee_id');
    }

    /**
     * Get the attendance records for the employee.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Get the leave requests for the employee.
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get the employment contracts for the employee.
     */
    public function contracts()
    {
        return $this->hasMany(EmploymentContract::class);
    }

    /**
     * Get the employee documents.
     */
    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * Get the performance reviews for the employee.
     */
    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class, 'employee_id');
    }

    /**
     * Get the performance reviews where the employee is the reviewer.
     */
    public function reviewsGiven()
    {
        return $this->hasMany(PerformanceReview::class, 'reviewer_employee_id');
    }

    /**
     * Get the salary records for the employee.
     */
    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    /**
     * Get the fullname of the employee.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Get the current salary for the employee.
     */
    public function getCurrentSalaryAttribute()
    {
        return $this->salaries()->where('is_current', true)->first();
    }

    /**
     * Get the company assignments for this employee.
     */
    public function companyAssignments()
    {
        return $this->hasMany(EmployeeCompanyAssignment::class);
    }

    /**
     * Get the active company assignments for this employee.
     */
    public function activeCompanyAssignments()
    {
        return $this->companyAssignments()->where('is_active', true);
    }

    /**
     * Assign this employee to a company.
     *
     * @param  Company|int  $company
     * @return EmployeeCompanyAssignment
     */
    public function assignToCompany($company, array $attributes = [])
    {
        $companyId = $company instanceof Company ? $company->id : $company;

        $defaults = [
            'start_date' => now(),
            'is_active' => true,
        ];

        return $this->companyAssignments()->create(array_merge($defaults, $attributes, [
            'company_id' => $companyId,
        ]));
    }

    /**
     * Check if this employee is assigned to a specific company.
     *
     * @param  Company|int  $company
     * @return bool
     */
    public function isAssignedToCompany($company)
    {
        $companyId = $company instanceof Company ? $company->id : $company;

        return $this->activeCompanyAssignments()
            ->where('company_id', $companyId)
            ->exists();
    }

    /**
     * Get companies this employee is assigned to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function assignedCompanies()
    {
        return $this->belongsToMany(Company::class, 'employee_company_assignments', 'employee_id', 'company_id')
            ->wherePivot('is_active', true);
    }

    /**
     * Sync user roles based on company assignments.
     *
     * @return void
     */
    public function syncUserRoles()
    {
        // Only proceed if the employee has a user account
        if (! $this->user) {
            return;
        }

        // Clear existing roles
        $this->user->syncRoles([]);

        // Assign roles based on company assignments
        foreach ($this->activeCompanyAssignments as $assignment) {
            if ($assignment->role) {
                // Assign the role specified in the assignment
                $this->user->assignRole($assignment->role);
            }
        }

        // If no roles were assigned, assign the default employee role
        if ($this->user->roles->isEmpty() && config('hr.default_employee_role')) {
            $this->user->assignRole(config('hr.default_employee_role'));
        }
    }
}

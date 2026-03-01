<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Traits\HasNotificationPreferences;

class Employee extends Model
{
    use HasFactory, HasNotificationPreferences;

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
        'employee_photo',
        'first_name',
        'last_name',
        'other_names',
        'full_name',
        'gender',
        'dob',
        'phone',
        'alt_phone',
        'whatsapp_no',
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
        'residential_gps',
        'system_access_requested',
        'system_access_approved_at',
        'next_of_kin',
        'bank_account_holder_name',
        'bank_name',
        'bank_account_no',
        'bank_branch',
        'bank_sort_code',
        'national_id_type',
        'national_id_photos',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'dob' => 'date',
            'next_of_kin' => 'json',
            'national_id_photos' => 'array',

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

            // Generate Employee code
            if (empty($employee->employee_code)) {
                $lastEmployee = static::orderBy('id', 'desc')->first();
                $lastId = $lastEmployee ? $lastEmployee->id : 0;
                $employee->employee_code = '#EMP'.str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
            }

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
     * Get the leave balances for the employee.
     */
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
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
     * Get communication name for notification system
     */
    public function getCommName(): string
    {
        return $this->full_name;
    }

    /**
     * Get communication email for notification system
     */
    public function getCommEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Get communication phone for notification system
     */
    public function getCommPhone(): ?string
    {
        return $this->phone ?? $this->whatsapp_no;
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
                try {
                    // Assign the role specified in the assignment
                    $this->user->assignRole($assignment->role);
                } catch (\Exception $e) {
                    // Log the error but continue with other assignments
                    Log::warning("Failed to assign role '{$assignment->role}' to user {$this->user->id}: {$e->getMessage()}");
                }
            }
        }

        // If no roles were assigned, assign the default employee role
        if ($this->user->roles->isEmpty() && config('hr.default_employee_role')) {
            $defaultRole = config('hr.default_employee_role');
            try {
                $this->user->assignRole($defaultRole);
            } catch (\Exception $e) {
                // Log the error but don't break the application
                Log::warning("Failed to assign default role '{$defaultRole}' to user {$this->user->id}: {$e->getMessage()}");

                // Fallback: assign a basic role that should exist
                try {
                    $this->user->assignRole('user');
                } catch (\Exception $fallbackException) {
                    Log::error("Failed to assign fallback role 'user' to user {$this->user->id}: {$fallbackException->getMessage()}");
                }
            }
        }
    }

    /**
     * Request system access for this employee.
     */
    public function requestSystemAccess(): void
    {
        if ($this->user_id) {
            throw new \Exception('Employee already has a user account');
        }

        if (! $this->email) {
            throw new \Exception('Employee email is required to request system access');
        }

        $this->update([
            'system_access_requested' => true,
            'system_access_approved_at' => null,
        ]);

        // In a real implementation, you would trigger a notification to admins here
        Log::info("System access requested for employee: {$this->full_name} ({$this->email})");
    }

    /**
     * Approve system access and create user account.
     */
    public function approveSystemAccess(?string $password = null): \App\Models\User
    {
        if (! $this->system_access_requested) {
            throw new \Exception('System access not requested');
        }

        if ($this->user_id) {
            throw new \Exception('Employee already has a user account');
        }

        if (! $this->email) {
            throw new \Exception('Employee email is required to create user account');
        }

        $password = $password ?? \Illuminate\Support\Str::random(config('hr.default_password_length', 12));

        $user = \App\Models\User::create([
            'name' => $this->full_name,
            'email' => $this->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'current_company_id' => $this->company_id,
        ]);

        $this->update([
            'user_id' => $user->id,
            'system_access_approved_at' => now(),
            'system_access_requested' => false,
        ]);

        $this->syncUserRoles();

        Log::info("System access approved for employee: {$this->full_name}, user ID: {$user->id}");

        return $user;
    }

    /**
     * Deny system access request.
     */
    public function denySystemAccess(): void
    {
        if (! $this->system_access_requested) {
            throw new \Exception('System access not requested');
        }

        $this->update([
            'system_access_requested' => false,
            'system_access_approved_at' => null,
        ]);

        Log::info("System access denied for employee: {$this->full_name}");
    }

    /**
     * Create user account manually (bypass approval workflow).
     */
    public function createUserAccount(?string $password = null): \App\Models\User
    {
        if ($this->user_id) {
            throw new \Exception('Employee already has a user account');
        }

        if (! $this->email) {
            throw new \Exception('Employee email is required to create user account');
        }

        $password = $password ?? \Illuminate\Support\Str::random(config('hr.default_password_length', 12));

        $user = \App\Models\User::create([
            'name' => $this->full_name,
            'email' => $this->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'current_company_id' => $this->company_id,
        ]);

        $this->update(['user_id' => $user->id]);
        $this->syncUserRoles();

        Log::info("User account created for employee: {$this->full_name}, user ID: {$user->id}");

        return $user;
    }
}

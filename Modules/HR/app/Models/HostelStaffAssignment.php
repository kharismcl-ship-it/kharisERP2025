<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Models\Hostel;

class HostelStaffAssignment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hostel_staff_assignments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'employee_id',
        'hostel_id',
        'role',
        'assigned_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            // Ensure company_id is always set
            if (empty($assignment->company_id)) {
                if (! empty($assignment->hostel_id)) {
                    $hostel = Hostel::find($assignment->hostel_id);
                    if ($hostel) {
                        $assignment->company_id = $hostel->company_id;
                    }
                } elseif (! empty($assignment->employee_id)) {
                    $employee = Employee::find($assignment->employee_id);
                    if ($employee) {
                        $assignment->company_id = $employee->company_id;
                    }
                }
            }
        });
    }

    /**
     * Get the company that owns the assignment.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee assigned to the hostel.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the hostel the employee is assigned to.
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Create a new factory for hostel staff assignment.
     *
     * @return \Modules\HR\Models\EmployeeCompanyAssignment
     */
    public static function create(array $attributes = [])
    {
        // If we have the required hostel assignment fields,
        // also create an EmployeeCompanyAssignment record
        if (isset($attributes['employee_id']) && isset($attributes['hostel_id'])) {
            // Get the hostel to find its company
            $hostel = Hostel::find($attributes['hostel_id']);
            if ($hostel) {
                // Check if an assignment already exists for the same employee, company, and date
                $startDate = date('Y-m-d', strtotime($attributes['assigned_at'] ?? now()));

                $existingAssignment = EmployeeCompanyAssignment::where([
                    'employee_id' => $attributes['employee_id'],
                    'company_id' => $hostel->company_id,
                    'start_date' => $startDate,
                ])->first();

                // Only create if it doesn't already exist
                if (! $existingAssignment) {
                    // Create the EmployeeCompanyAssignment
                    EmployeeCompanyAssignment::create([
                        'employee_id' => $attributes['employee_id'],
                        'company_id' => $hostel->company_id,
                        'start_date' => $attributes['assigned_at'] ?? now(),
                        'end_date' => $attributes['expires_at'] ?? null,
                        'assignment_reason' => 'Hostel assignment: '.($attributes['role'] ?? 'Staff'),
                        'is_active' => true,
                    ]);
                }

                // Set the company_id for the hostel assignment
                $attributes['company_id'] = $hostel->company_id;
            }
        }

        // Ensure company_id is set to avoid the SQL error
        if (! isset($attributes['company_id']) && isset($attributes['hostel_id'])) {
            $hostel = Hostel::find($attributes['hostel_id']);
            if ($hostel) {
                $attributes['company_id'] = $hostel->company_id;
            }
        }

        if (! isset($attributes['company_id']) && isset($attributes['employee_id'])) {
            $employee = Employee::find($attributes['employee_id']);
            if ($employee) {
                $attributes['company_id'] = $employee->company_id;
            }
        }

        return parent::create($attributes);
    }
}

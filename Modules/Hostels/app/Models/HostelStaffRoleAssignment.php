<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Models\Employee;

class HostelStaffRoleAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hostel_occupant_id',
        'hostel_id',
        'employee_id',
        'role_id',
        'start_date',
        'end_date',
        'is_primary',
        'assignment_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_primary' => 'boolean',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function role()
    {
        return $this->belongsTo(HostelStaffRole::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('end_date')
                ->orWhere('end_date', '>=', now());
        });
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeForHostel($query, $hostelId)
    {
        return $query->where('hostel_id', $hostelId);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}

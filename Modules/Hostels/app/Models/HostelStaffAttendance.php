<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Models\Employee;

class HostelStaffAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_staff_attendance';

    protected $fillable = [
        'hostel_occupant_id',
        'hostel_id',
        'employee_id',
        'attendance_date',
        'clock_in_time',
        'clock_out_time',
        'hours_worked',
        'status',
        'notes',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'clock_in_time' => 'datetime:H:i',
        'clock_out_time' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeForHostel($query, $hostelId)
    {
        return $query->where('hostel_id', $hostelId);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    public function calculateHoursWorked(): void
    {
        if ($this->clock_in_time && $this->clock_out_time) {
            $start = \Carbon\Carbon::parse($this->clock_in_time);
            $end = \Carbon\Carbon::parse($this->clock_out_time);
            $this->hours_worked = $end->diffInHours($start, true);
        }
    }
}

<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class HostelStaffShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'employee_id',
        'shift_type',
        'start_time',
        'end_time',
        'shift_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'shift_date' => 'date',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeScheduledForDate($query, $date)
    {
        return $query->where('shift_date', $date);
    }

    public function scopeForHostel($query, $hostelId)
    {
        return $query->where('hostel_id', $hostelId);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function markAsInProgress()
    {
        $this->update(['status' => 'in_progress']);
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }
}

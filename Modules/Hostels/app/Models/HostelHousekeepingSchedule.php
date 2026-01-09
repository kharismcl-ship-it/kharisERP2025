<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class HostelHousekeepingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'room_id',
        'assigned_employee_id',
        'schedule_date',
        'cleaning_type',
        'status',
        'started_at',
        'completed_at',
        'notes',
        'quality_score',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('schedule_date', $date);
    }

    public function scopeForRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    public function markAsInProgress()
    {
        $this->update(['status' => 'in_progress', 'started_at' => now()]);
    }

    public function markAsCompleted($qualityScore = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'quality_score' => $qualityScore,
        ]);
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->schedule_date < now()->format('Y-m-d');
    }
}

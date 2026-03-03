<?php

namespace Modules\Construction\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Construction\Events\WorkerCheckedIn;
use Modules\Construction\Events\WorkerCheckedOut;

class WorkerAttendance extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'construction_worker_id',
        'construction_project_id',
        'date',
        'check_in_time',
        'check_out_time',
        'hours_worked',
        'per_diem_amount',
        'attendance_status',
        'is_approved',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'date'        => 'date',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'hours_worked'    => 'decimal:2',
        'per_diem_amount' => 'decimal:2',
    ];

    const ATTENDANCE_STATUSES = ['present', 'absent', 'half_day', 'excused'];

    protected static function booted(): void
    {
        static::creating(function (self $attendance) {
            if ($attendance->check_in_time && !$attendance->check_out_time) {
                $worker = $attendance->worker;
                if ($worker) {
                    WorkerCheckedIn::dispatch($worker, $attendance);
                }
            }
        });

        static::updating(function (self $attendance) {
            if ($attendance->isDirty('check_out_time') && $attendance->check_out_time) {
                // Auto-calculate hours worked
                if ($attendance->check_in_time) {
                    $in  = \Carbon\Carbon::parse($attendance->check_in_time);
                    $out = \Carbon\Carbon::parse($attendance->check_out_time);
                    $hours = round($out->diffInMinutes($in) / 60, 2);
                    $attendance->hours_worked = $hours;
                }

                // Auto-calculate per diem based on attendance_status
                $worker = $attendance->worker;
                if ($worker) {
                    $factor = match ($attendance->attendance_status) {
                        'present'  => 1.0,
                        'half_day' => 0.5,
                        default    => 0.0,
                    };
                    $attendance->per_diem_amount = round((float) $worker->daily_rate * $factor, 2);
                }

                WorkerCheckedOut::dispatch($worker, $attendance);
            }
        });
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(ConstructionWorker::class, 'construction_worker_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

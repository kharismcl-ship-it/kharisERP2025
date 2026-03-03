<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

class FarmWorkerAttendance extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_worker_attendances';

    protected $fillable = [
        'farm_id',
        'farm_worker_id',
        'company_id',
        'attendance_date',
        'status',
        'hours_worked',
        'overtime_hours',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'hours_worked'    => 'decimal:2',
        'overtime_hours'  => 'decimal:2',
    ];

    const STATUSES = ['present', 'absent', 'half_day', 'leave'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function farmWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}

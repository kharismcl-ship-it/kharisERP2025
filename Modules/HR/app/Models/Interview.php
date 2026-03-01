<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    protected $table = 'hr_interviews';

    protected $fillable = [
        'applicant_id', 'interview_type', 'scheduled_at', 'location',
        'status', 'result', 'score', 'feedback', 'interviewer_employee_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'score'        => 'integer',
    ];

    const TYPES = [
        'phone_screening' => 'Phone Screening',
        'technical'       => 'Technical',
        'hr'              => 'HR Interview',
        'panel'           => 'Panel Interview',
        'final'           => 'Final Interview',
    ];

    const STATUSES = ['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'no_show' => 'No Show'];
    const RESULTS  = ['passed' => 'Passed', 'failed' => 'Failed', 'pending' => 'Pending'];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'interviewer_employee_id');
    }
}
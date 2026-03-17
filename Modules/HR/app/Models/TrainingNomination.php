<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingNomination extends Model
{
    protected $table = 'hr_training_nominations';

    protected $fillable = [
        'training_program_id', 'employee_id', 'company_id', 'status', 'completion_date', 'score', 'notes',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'score'           => 'decimal:2',
    ];

    const STATUSES = [
        'nominated'  => 'Nominated',
        'confirmed'  => 'Confirmed',
        'attended'   => 'Attended',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
    ];

    public function trainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
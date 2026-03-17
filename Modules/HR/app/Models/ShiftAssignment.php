<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftAssignment extends Model
{
    protected $table = 'hr_shift_assignments';

    protected $fillable = [
        'shift_id', 'employee_id', 'company_id', 'effective_from', 'effective_to', 'notes',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to'   => 'date',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
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
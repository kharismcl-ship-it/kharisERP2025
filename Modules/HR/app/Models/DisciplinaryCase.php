<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class DisciplinaryCase extends Model
{
    protected $table = 'hr_disciplinary_cases';

    protected $fillable = [
        'company_id', 'employee_id', 'type', 'incident_date', 'incident_description',
        'action_taken', 'status', 'resolution_date', 'resolution_notes', 'handled_by_employee_id',
    ];

    protected $casts = [
        'incident_date'   => 'date',
        'resolution_date' => 'date',
    ];

    const TYPES = [
        'verbal_warning'  => 'Verbal Warning',
        'written_warning' => 'Written Warning',
        'final_warning'   => 'Final Warning',
        'suspension'      => 'Suspension',
        'termination'     => 'Termination',
    ];

    const STATUSES = [
        'open'         => 'Open',
        'under_review' => 'Under Review',
        'resolved'     => 'Resolved',
        'appealed'     => 'Appealed',
        'closed'       => 'Closed',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'handled_by_employee_id');
    }
}
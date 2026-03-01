<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class GrievanceCase extends Model
{
    protected $table = 'hr_grievance_cases';

    protected $fillable = [
        'company_id', 'employee_id', 'grievance_type', 'filed_date', 'description',
        'status', 'resolution', 'resolution_date', 'is_anonymous', 'assigned_to_employee_id',
    ];

    protected $casts = [
        'filed_date'      => 'date',
        'resolution_date' => 'date',
        'is_anonymous'    => 'boolean',
    ];

    const STATUSES = [
        'filed'               => 'Filed',
        'under_investigation' => 'Under Investigation',
        'hearing_scheduled'   => 'Hearing Scheduled',
        'resolved'            => 'Resolved',
        'closed'              => 'Closed',
        'escalated'           => 'Escalated',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to_employee_id');
    }
}
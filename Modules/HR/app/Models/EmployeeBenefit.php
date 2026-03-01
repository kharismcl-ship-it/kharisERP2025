<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBenefit extends Model
{
    protected $table = 'hr_employee_benefits';

    protected $fillable = [
        'benefit_type_id', 'employee_id', 'start_date', 'end_date', 'status',
        'employer_contribution_override', 'employee_contribution_override', 'notes',
    ];

    protected $casts = [
        'start_date'                      => 'date',
        'end_date'                        => 'date',
        'employer_contribution_override'  => 'decimal:2',
        'employee_contribution_override'  => 'decimal:2',
    ];

    const STATUSES = ['pending' => 'Pending', 'active' => 'Active', 'inactive' => 'Inactive'];

    public function benefitType(): BelongsTo
    {
        return $this->belongsTo(BenefitType::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
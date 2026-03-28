<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class EmployeeSkill extends Model
{
    protected $table = 'hr_employee_skills';

    protected $fillable = [
        'company_id',
        'employee_id',
        'skill_id',
        'proficiency_level',
        'acquired_date',
        'expiry_date',
        'verified_by_employee_id',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'acquired_date' => 'date',
        'expiry_date'   => 'date',
        'verified_at'   => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'verified_by_employee_id');
    }

    public function getProficiencyLabelAttribute(): string
    {
        return Skill::proficiencyLabel($this->proficiency_level);
    }
}
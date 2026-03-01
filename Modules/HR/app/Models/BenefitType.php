<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class BenefitType extends Model
{
    protected $table = 'hr_benefit_types';

    protected $fillable = [
        'company_id', 'name', 'category', 'description', 'provider',
        'employer_contribution', 'employee_contribution_required',
        'employee_contribution', 'is_taxable', 'is_active',
    ];

    protected $casts = [
        'employer_contribution'          => 'decimal:2',
        'employee_contribution'          => 'decimal:2',
        'employee_contribution_required' => 'boolean',
        'is_taxable'                     => 'boolean',
        'is_active'                      => 'boolean',
    ];

    const CATEGORIES = [
        'health'      => 'Health Insurance',
        'insurance'   => 'Life Insurance',
        'transport'   => 'Transport',
        'housing'     => 'Housing',
        'education'   => 'Education',
        'retirement'  => 'Retirement Plan',
        'other'       => 'Other',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employeeBenefits(): HasMany
    {
        return $this->hasMany(EmployeeBenefit::class);
    }
}
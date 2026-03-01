<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class DeductionType extends Model
{
    protected $table = 'hr_deduction_types';

    protected $fillable = [
        'company_id', 'name', 'code', 'category', 'calculation_type',
        'default_amount', 'percentage_value', 'gl_account_code',
        'description', 'is_active',
    ];

    protected $casts = [
        'default_amount'   => 'decimal:2',
        'percentage_value' => 'decimal:4',
        'is_active'        => 'boolean',
    ];

    const CATEGORIES = [
        'tax'             => 'Income Tax (PAYE)',
        'social_security' => 'Social Security (SSNIT)',
        'pension'         => 'Pension',
        'loan'            => 'Loan Repayment',
        'voluntary'       => 'Voluntary Deduction',
        'other'           => 'Other',
    ];

    const CALCULATION_TYPES = ['fixed' => 'Fixed Amount', 'percentage' => 'Percentage of Basic'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
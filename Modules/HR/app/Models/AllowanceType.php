<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class AllowanceType extends Model
{
    use BelongsToCompany;

    protected $table = 'hr_allowance_types';

    protected $fillable = [
        'company_id', 'name', 'code', 'calculation_type', 'default_amount',
        'percentage_value', 'is_taxable', 'is_pensionable', 'gl_account_code',
        'description', 'is_active',
    ];

    protected $casts = [
        'default_amount'    => 'decimal:2',
        'percentage_value'  => 'decimal:4',
        'is_taxable'        => 'boolean',
        'is_pensionable'    => 'boolean',
        'is_active'         => 'boolean',
    ];

    const CALCULATION_TYPES = ['fixed' => 'Fixed Amount', 'percentage' => 'Percentage of Basic'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

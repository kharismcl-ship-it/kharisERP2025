<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

class TaxRate extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'rate',
        'type',
        'applies_to',
        'is_active',
    ];

    protected $casts = [
        'rate'      => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        'vat'         => 'VAT',
        'nhil'        => 'NHIL',
        'getf'        => 'GETFund',
        'withholding' => 'Withholding Tax',
        'other'       => 'Other',
    ];

    public const APPLIES_TO = [
        'income'  => 'Income / Sales',
        'expense' => 'Expense / Purchase',
        'both'    => 'Both',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /** Rate as a multiplier, e.g. 15% → 0.15 */
    public function multiplier(): float
    {
        return (float) $this->rate / 100;
    }

    /** Calculate tax amount for a given subtotal */
    public function calculate(float $subtotal): float
    {
        return round($subtotal * $this->multiplier(), 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

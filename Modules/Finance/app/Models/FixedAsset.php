<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

class FixedAsset extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'category_id',
        'asset_code',
        'name',
        'description',
        'acquisition_date',
        'cost',
        'residual_value',
        'accumulated_depreciation',
        'depreciation_start_date',
        'disposal_date',
        'disposal_amount',
        'status',
        'location',
        'serial_number',
    ];

    protected $casts = [
        'acquisition_date'          => 'date',
        'depreciation_start_date'   => 'date',
        'disposal_date'             => 'date',
        'cost'                      => 'decimal:2',
        'residual_value'            => 'decimal:2',
        'accumulated_depreciation'  => 'decimal:2',
        'disposal_amount'           => 'decimal:2',
    ];

    public const STATUSES = [
        'active'      => 'Active',
        'disposed'    => 'Disposed',
        'written_off' => 'Written Off',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    /** Net book value = cost - accumulated depreciation */
    public function netBookValue(): float
    {
        return (float) $this->cost - (float) $this->accumulated_depreciation;
    }

    /** Monthly straight-line depreciation amount */
    public function monthlyDepreciation(): float
    {
        $depreciableAmount = (float) $this->cost - (float) $this->residual_value;
        $usefulLifeMonths  = (float) ($this->category->useful_life_years ?? 5) * 12;

        if ($usefulLifeMonths <= 0) {
            return 0;
        }

        return round($depreciableAmount / $usefulLifeMonths, 2);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

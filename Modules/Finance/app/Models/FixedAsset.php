<?php

namespace Modules\Finance\Models;

use EduardoRibeiroDev\FilamentLeaflet\Concerns\HasGeoJsonFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;
use Modules\HR\Models\Employee;

class FixedAsset extends Model
{
    use BelongsToCompany, HasGeoJsonFile;

    protected $fillable = [
        'company_id',
        'custodian_employee_id',
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
        'photo',
        // Warranty
        'warranty_expiry_date',
        'warranty_vendor',
        'warranty_reference',
        // Insurance
        'insurance_policy_number',
        'insurance_provider',
        'insurance_value',
        'insurance_expiry_date',
        // Map
        'latitude',
        'longitude',
        'geometry',
    ];

    protected $casts = [
        'acquisition_date'         => 'date',
        'depreciation_start_date'  => 'date',
        'disposal_date'            => 'date',
        'warranty_expiry_date'     => 'date',
        'insurance_expiry_date'    => 'date',
        'cost'                     => 'decimal:2',
        'residual_value'           => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'disposal_amount'          => 'decimal:2',
        'insurance_value'          => 'decimal:2',
        'latitude'                 => 'float',
        'longitude'                => 'float',
        'geometry'                 => 'array',
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

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'custodian_employee_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(FixedAssetDocument::class);
    }

    public function depreciationRuns(): HasMany
    {
        return $this->hasMany(FixedAssetDepreciationRun::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(FixedAssetMaintenanceRecord::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(FixedAssetTransfer::class);
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

    // ── HasGeoJsonFile overrides ──────────────────────────────────────────────

    /** Column that holds the GeoJSON data (used by HasGeoJsonFile trait). */
    public function getGeoJsonFileAttributeName(): string
    {
        return 'geometry';
    }

    /**
     * Return a data-URI so MapEntry can load the inline GeoJSON FeatureCollection
     * without needing a dedicated route or file upload.
     */
    public function getGeoJsonUrl(): ?string
    {
        if (empty($this->geometry)) {
            return null;
        }

        $json = is_array($this->geometry)
            ? json_encode($this->geometry)
            : $this->geometry;

        return 'data:application/json;base64,' . base64_encode($json);
    }
}

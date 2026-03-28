<?php

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToCompany;

class Vendor extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'vendors';

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'contact_person',
        'contact_phone',
        'contact_email',
        'tax_number',
        'payment_terms',
        'currency',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'status',
        'notes',
        'is_local',
        'diversity_class',
        'local_content_score',
    ];

    protected $casts = [
        'payment_terms'       => 'integer',
        'is_local'            => 'boolean',
        'local_content_score' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $vendor) {
            if (empty($vendor->slug)) {
                $vendor->slug = Str::slug($vendor->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function performanceRecords(): HasMany
    {
        return $this->hasMany(VendorPerformanceRecord::class);
    }

    public function scorecards(): HasMany
    {
        return $this->hasMany(VendorScorecard::class);
    }

    public function latestScorecard(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(VendorScorecard::class)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month');
    }

    public function overallScore(): float
    {
        return (float) ($this->latestScorecard?->overall_score ?? 0.0);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(VendorCertificate::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(ProcurementContract::class);
    }

    public function catalogs(): HasMany
    {
        return $this->hasMany(VendorCatalog::class);
    }
}

<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToCompany;

class SalesOrganization extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'industry',
        'website',
        'email',
        'phone',
        'city',
        'country',
        'credit_limit',
        'payment_terms',
        'currency',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $org) {
            if (empty($org->slug)) {
                $org->slug = Str::slug($org->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SalesContact::class, 'organization_id');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(SalesOpportunity::class, 'organization_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(SalesQuotation::class, 'organization_id');
    }
}
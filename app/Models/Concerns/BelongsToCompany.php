<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Adds automatic tenant scoping to any Eloquent model that owns a
 * company_id column.
 *
 * When the company-admin panel is active the TenantScope restricts
 * queries to the current tenant's company (and all its subsidiaries
 * when the tenant is a group/HQ company).  The admin panel and CLI
 * contexts are unaffected — they always see all data.
 *
 * Usage: add `use BelongsToCompany;` inside the model class.
 * The model must already have a company_id column.
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Every model using this trait has a company_id FK.
     * Filament's tenant ownership check requires a 'company' relationship.
     * Models that define their own company() override this automatically.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
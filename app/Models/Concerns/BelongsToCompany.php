<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;

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
}
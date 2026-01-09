<?php

namespace Modules\HR\Traits;

use App\Models\Company;

trait BelongsToCompany
{
    /**
     * Scope a query to only include records for a specific company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get the company that owns the record.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

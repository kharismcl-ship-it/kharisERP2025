<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmInputChemical extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_input_chemicals';

    protected $fillable = [
        'company_id',
        'product_name',
        'brand_name',
        'active_ingredient',
        'chemical_class',
        'phi_days',
        'mrl_mg_per_kg',
        'approved_for_organic',
        'registration_number',
        'application_rate_per_ha',
        'safety_notes',
        'is_restricted',
        'is_active',
    ];

    protected $casts = [
        'mrl_mg_per_kg'        => 'float',
        'approved_for_organic' => 'boolean',
        'is_restricted'        => 'boolean',
        'is_active'            => 'boolean',
    ];

    const CHEMICAL_CLASSES = [
        'Herbicide', 'Fungicide', 'Insecticide', 'Fertilizer', 'Biopesticide',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeApprovedOrganic(Builder $query): Builder
    {
        return $query->where('approved_for_organic', true);
    }
}
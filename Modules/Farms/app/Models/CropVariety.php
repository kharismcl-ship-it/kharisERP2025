<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class CropVariety extends Model
{
    protected $table = 'crop_varieties';

    protected $fillable = [
        'company_id',
        'crop_name', 'variety_name', 'seed_supplier', 'description',
        'typical_yield_per_acre', 'yield_unit',
        'growing_period_days', 'planting_season',
        'spacing_cm', 'seed_rate_per_acre', 'seed_unit',
        'is_active', 'notes',
    ];

    protected $casts = [
        'typical_yield_per_acre' => 'decimal:3',
        'spacing_cm'             => 'decimal:2',
        'seed_rate_per_acre'     => 'decimal:3',
        'growing_period_days'    => 'integer',
        'is_active'              => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
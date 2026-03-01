<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmBudget extends Model
{
    protected $table = 'farm_budgets';

    protected $fillable = [
        'farm_id', 'crop_cycle_id', 'company_id',
        'budget_name', 'budget_year', 'budget_month', 'category',
        'budgeted_amount', 'actual_amount', 'notes',
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'actual_amount'   => 'decimal:2',
    ];

    const CATEGORIES = [
        'seeds', 'fertilizer', 'pesticide', 'labour',
        'equipment', 'irrigation', 'transport', 'general',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function getVarianceAttribute(): float
    {
        return (float) $this->actual_amount - (float) $this->budgeted_amount;
    }

    public function getVariancePctAttribute(): ?float
    {
        if (! $this->budgeted_amount || $this->budgeted_amount == 0) {
            return null;
        }
        return round(($this->variance / $this->budgeted_amount) * 100, 1);
    }
}
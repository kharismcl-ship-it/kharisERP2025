<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmPasture extends Model
{
    protected $table = 'farm_pastures';

    protected $fillable = [
        'company_id', 'farm_id', 'pasture_name', 'pasture_type', 'area_ha',
        'current_foo_kg_ha', 'target_foo_kg_ha', 'carrying_capacity_au_ha',
        'is_occupied', 'current_batch_id', 'last_grazed_date', 'available_from_date',
        'rest_days_required', 'notes', 'is_active',
    ];

    protected $casts = [
        'last_grazed_date'    => 'date',
        'available_from_date' => 'date',
        'is_occupied'         => 'boolean',
        'is_active'           => 'boolean',
        'current_foo_kg_ha'   => 'float',
        'area_ha'             => 'float',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function currentBatch(): BelongsTo
    {
        return $this->belongsTo(LivestockBatch::class, 'current_batch_id');
    }

    public function grazingEvents(): HasMany
    {
        return $this->hasMany(FarmGrazingEvent::class, 'farm_pasture_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_occupied', false)->where('is_active', true);
    }

    public function isLowFoo(): bool
    {
        if (!$this->current_foo_kg_ha || !$this->target_foo_kg_ha) {
            return false;
        }
        return $this->current_foo_kg_ha < $this->target_foo_kg_ha;
    }

    public function daysInRest(): ?int
    {
        if (!$this->last_grazed_date) {
            return null;
        }
        return $this->last_grazed_date->diffInDays(now());
    }
}
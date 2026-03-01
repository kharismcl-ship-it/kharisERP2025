<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostCentre extends Model
{
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CostCentre::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CostCentre::class, 'parent_id');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'cost_centre_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

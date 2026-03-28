<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmAgronomist extends Model
{
    protected $table = 'farm_agronomists';

    protected $fillable = [
        'company_id',
        'name',
        'title',
        'organization',
        'phone',
        'email',
        'specialization',
        'assigned_farm_ids',
        'is_active',
    ];

    protected $casts = [
        'assigned_farm_ids' => 'array',
        'is_active'         => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(FarmAgronomistVisit::class);
    }
}
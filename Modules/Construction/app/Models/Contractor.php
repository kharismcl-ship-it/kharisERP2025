<?php

namespace Modules\Construction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Company;

class Contractor extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'contact_person',
        'phone',
        'email',
        'address',
        'specialization',
        'license_number',
        'license_expiry',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'is_active'      => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $contractor) {
            if (empty($contractor->slug)) {
                $contractor->slug = Str::slug($contractor->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmCertification extends Model
{
    protected $table = 'farm_certifications';

    protected $fillable = [
        'company_id',
        'farm_id',
        'certification_type',
        'certifying_body',
        'certificate_number',
        'issued_date',
        'expiry_date',
        'status',
        'scope',
        'document_path',
        'renewal_reminder_days',
        'notes',
    ];

    protected $casts = [
        'issued_date'           => 'date',
        'expiry_date'           => 'date',
        'renewal_reminder_days' => 'integer',
        'status'                => 'string',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(FarmComplianceChecklist::class, 'farm_certification_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 60): Builder
    {
        return $query->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }

    public function daysUntilExpiry(): ?int
    {
        if ($this->expiry_date === null) {
            return null;
        }

        return (int) now()->diffInDays($this->expiry_date, false);
    }
}
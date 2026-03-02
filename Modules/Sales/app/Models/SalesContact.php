<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class SalesContact extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'organization_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'whatsapp_number',
        'job_title',
        'tags',
        'notes',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(SalesOrganization::class, 'organization_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SalesActivity::class, 'related_id')->where('related_type', self::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(SalesOpportunity::class, 'contact_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(SalesQuotation::class, 'contact_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'contact_id');
    }
}
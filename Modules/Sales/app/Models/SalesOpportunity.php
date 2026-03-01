<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOpportunity extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'contact_id',
        'organization_id',
        'estimated_value',
        'probability_pct',
        'stage',
        'expected_close_date',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'estimated_value'     => 'decimal:2',
        'probability_pct'     => 'integer',
        'expected_close_date' => 'date',
    ];

    const STAGES = [
        'prospecting',
        'qualification',
        'proposal',
        'negotiation',
        'closed_won',
        'closed_lost',
    ];

    public function getWeightedValueAttribute(): float
    {
        return round($this->estimated_value * ($this->probability_pct / 100), 2);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(SalesContact::class, 'contact_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(SalesOrganization::class, 'organization_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOpportunityItem::class, 'opportunity_id');
    }
}
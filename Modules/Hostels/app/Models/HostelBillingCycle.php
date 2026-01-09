<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelBillingCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'name',
        'cycle_type',
        'start_date',
        'end_date',
        'billing_date',
        'due_date',
        'grace_period_days',
        'late_fee_percentage',
        'late_fee_fixed_amount',
        'is_active',
        'auto_generate',
        'auto_post_to_gl',
        'charge_types',
        'include_utilities',
        'include_deposits',
        'billing_rules',
        'notification_settings',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'billing_date' => 'date',
        'due_date' => 'date',
        'grace_period_days' => 'integer',
        'late_fee_percentage' => 'decimal:4',
        'late_fee_fixed_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'auto_generate' => 'boolean',
        'auto_post_to_gl' => 'boolean',
        'charge_types' => 'array',
        'include_utilities' => 'boolean',
        'include_deposits' => 'boolean',
        'billing_rules' => 'array',
        'notification_settings' => 'array',
    ];

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function utilityCharges(): HasMany
    {
        return $this->hasMany(\Modules\Hostels\Models\HostelUtilityCharge::class, 'billing_cycle_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAutoGenerate($query)
    {
        return $query->where('auto_generate', true);
    }
}

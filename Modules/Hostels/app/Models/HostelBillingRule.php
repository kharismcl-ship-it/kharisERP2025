<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelBillingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'name',
        'rule_type',
        'calculation_method',
        'amount',
        'gl_account_code',
        'is_active',
        'auto_apply',
        'conditions',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'is_active' => 'boolean',
        'auto_apply' => 'boolean',
        'conditions' => 'array',
    ];

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAutoApply($query)
    {
        return $query->where('auto_apply', true);
    }

    public function calculateCharge($baseAmount = 0, $daysLate = 0, $units = 0): float
    {
        return match ($this->calculation_method) {
            'fixed' => $this->amount,
            'percentage' => $baseAmount * ($this->amount / 100),
            'per_day' => $daysLate * $this->amount,
            'per_unit' => $units * $this->amount,
            default => 0
        };
    }
}

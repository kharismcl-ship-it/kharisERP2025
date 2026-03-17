<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmCoupon extends Model
{
    protected $table = 'farm_coupons';

    protected $fillable = [
        'company_id', 'code', 'type', 'discount_value',
        'min_order_amount', 'max_uses', 'uses_count',
        'valid_from', 'valid_to', 'is_active', 'description',
    ];

    protected $casts = [
        'discount_value'   => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active'        => 'boolean',
        'valid_from'       => 'date',
        'valid_to'         => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if this coupon is valid for a given order total.
     */
    public function isValidFor(float $orderTotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->valid_from && now()->startOfDay()->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to && now()->startOfDay()->gt($this->valid_to)) {
            return false;
        }

        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }

        if ($this->min_order_amount !== null && $orderTotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            return round($subtotal * ((float) $this->discount_value / 100), 2);
        }

        return min((float) $this->discount_value, $subtotal);
    }
}

<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmB2bAccount extends Model
{
    protected $table = 'farm_b2b_accounts';

    protected $fillable = [
        'company_id', 'business_name', 'business_type', 'contact_name',
        'contact_phone', 'contact_email', 'business_address', 'tax_id', 'ghc_reg',
        'status', 'rejection_reason', 'approved_at',
        'discount_percent', 'payment_terms', 'credit_limit', 'credit_used',
        'internal_notes',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'credit_limit'     => 'decimal:2',
        'credit_used'      => 'decimal:2',
        'approved_at'      => 'datetime',
    ];

    const TYPES = ['restaurant', 'hotel', 'caterer', 'school', 'supermarket', 'other'];
    const STATUSES = ['pending', 'approved', 'rejected'];
    const PAYMENT_TERMS = ['prepay', 'net7', 'net14', 'net30'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(ShopCustomer::class, 'b2b_account_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(FarmOrder::class, 'b2b_account_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /** Outstanding credit balance used by this account. */
    public function availableCredit(): float
    {
        if (! $this->credit_limit) {
            return PHP_FLOAT_MAX; // unlimited
        }
        return max(0, (float) $this->credit_limit - (float) $this->credit_used);
    }
}

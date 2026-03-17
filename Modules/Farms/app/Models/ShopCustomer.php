<?php

namespace Modules\Farms\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ShopCustomer extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'default_address',
        'default_landmark',
        'referral_code',
        'is_b2b',
        'b2b_account_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_b2b'            => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(FarmOrder::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(FarmCustomerWishlist::class);
    }

    public function savedAddresses(): HasMany
    {
        return $this->hasMany(FarmSavedAddress::class)->orderByDesc('is_default');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(FarmSubscription::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(FarmReferral::class, 'referrer_id');
    }

    public function b2bAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FarmB2bAccount::class, 'b2b_account_id');
    }

    /** Wholesale discount % for this customer (0 if not B2B or not approved). */
    public function getB2bDiscountPercent(): float
    {
        if (! $this->is_b2b || ! $this->b2b_account_id) {
            return 0.0;
        }
        $account = $this->b2bAccount;
        if (! $account || ! $account->isApproved()) {
            return 0.0;
        }
        return (float) $account->discount_percent;
    }

    public function getLoyaltyBalance(int $companyId): int
    {
        return FarmLoyaltyPoint::getBalance($this->id, $companyId);
    }

    /** Generate and persist a unique referral code if not already set. */
    public function ensureReferralCode(): string
    {
        if (! $this->referral_code) {
            do {
                $code = strtoupper(substr(base_convert(bin2hex(random_bytes(4)), 16, 36), 0, 7));
            } while (static::where('referral_code', $code)->exists());

            $this->update(['referral_code' => $code]);
        }
        return $this->referral_code;
    }
}

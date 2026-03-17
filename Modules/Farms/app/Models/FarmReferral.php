<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmReferral extends Model
{
    protected $table = 'farm_referrals';

    protected $fillable = [
        'company_id', 'referrer_id', 'referred_id', 'credited_at',
    ];

    protected $casts = [
        'credited_at' => 'datetime',
    ];

    // Points awarded to the referrer when their referral's first order is paid
    const REFERRAL_POINTS = 50;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class, 'referred_id');
    }

    public function scopePending($query)
    {
        return $query->whereNull('credited_at');
    }
}

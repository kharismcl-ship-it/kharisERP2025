<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmSubscription extends Model
{
    protected $table = 'farm_subscriptions';

    protected $fillable = [
        'company_id', 'shop_customer_id',
        'frequency', 'status', 'items', 'subtotal',
        'delivery_type', 'delivery_address', 'delivery_landmark', 'notes',
        'next_order_date', 'last_order_date',
        'paused_at', 'cancelled_at',
    ];

    protected $casts = [
        'items'            => 'array',
        'subtotal'         => 'decimal:2',
        'next_order_date'  => 'date',
        'last_order_date'  => 'date',
        'paused_at'        => 'datetime',
        'cancelled_at'     => 'datetime',
    ];

    const FREQUENCIES = [
        'weekly'   => 'Weekly',
        'biweekly' => 'Every 2 Weeks',
        'monthly'  => 'Monthly',
    ];

    const STATUSES = ['active', 'paused', 'cancelled'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shopCustomer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class);
    }

    /** Advance next_order_date by frequency interval. */
    public function advanceNextOrderDate(): void
    {
        $this->next_order_date = match ($this->frequency) {
            'weekly'   => $this->next_order_date->addWeek(),
            'biweekly' => $this->next_order_date->addWeeks(2),
            'monthly'  => $this->next_order_date->addMonth(),
            default    => $this->next_order_date->addWeek(),
        };
        $this->last_order_date = now()->toDateString();
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDue($query)
    {
        return $query->where('next_order_date', '<=', now()->toDateString());
    }
}

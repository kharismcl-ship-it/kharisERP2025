<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Modules\Farms\Models\ShopCustomer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\PaymentsChannel\Traits\HasPayments;

class FarmOrder extends Model
{
    use HasPayments;

    protected $table = 'farm_orders';

    protected $fillable = [
        'ref', 'company_id', 'shop_customer_id',
        'customer_name', 'customer_email', 'customer_phone',
        'delivery_address', 'delivery_landmark', 'delivery_type',
        'preferred_delivery_date',
        'status', 'payment_status',
        'subtotal', 'delivery_fee', 'discount_amount', 'total',
        'coupon_code', 'notes', 'placed_at',
        'loyalty_points_redeemed', 'loyalty_discount', 'loyalty_points_earned',
        'is_b2b', 'b2b_account_id', 'po_number', 'payment_terms', 'b2b_discount_amount',
    ];

    protected $casts = [
        'placed_at'                => 'datetime',
        'preferred_delivery_date'  => 'date',
        'subtotal'                 => 'decimal:2',
        'delivery_fee'             => 'decimal:2',
        'discount_amount'          => 'decimal:2',
        'loyalty_discount'         => 'decimal:2',
        'total'                    => 'decimal:2',
        'loyalty_points_redeemed'  => 'integer',
        'loyalty_points_earned'    => 'integer',
        'is_b2b'                   => 'boolean',
        'b2b_discount_amount'      => 'decimal:2',
    ];

    const STATUSES = ['pending', 'confirmed', 'processing', 'ready', 'delivered', 'cancelled'];

    const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded'];

    // ── Relationships ────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shopCustomer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FarmOrderItem::class);
    }

    public function b2bAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FarmB2bAccount::class, 'b2b_account_id');
    }

    public function delivery(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FarmOrderDelivery::class);
    }

    // ── HasPayments implementation ───────────────────────────────────────────

    public function getPaymentDescription(): string
    {
        $shopName = 'Farm Shop';
        if ($this->company_id) {
            try {
                $settings = app(\Modules\Farms\Services\ShopSettingsService::class)->get($this->company_id);
                $shopName = $settings->shop_name ?? 'Farm Shop';
            } catch (\Throwable) {}
        }
        return "{$shopName} Order {$this->ref}";
    }

    public function getPaymentAmount(): float|int
    {
        return (float) $this->total;
    }

    public function getPaymentCurrency(): string
    {
        return 'GHS';
    }

    public function getPaymentCustomerName(): ?string
    {
        return $this->customer_name;
    }

    public function getPaymentCustomerEmail(): ?string
    {
        return $this->customer_email;
    }

    public function getPaymentCustomerPhone(): ?string
    {
        return $this->customer_phone;
    }

    // ── Ref generation ───────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->ref)) {
                $order->ref = static::generateRef();
            }
            if (empty($order->placed_at)) {
                $order->placed_at = now();
            }
        });
    }

    public static function generateRef(): string
    {
        $prefix = 'ORD-' . now()->format('Ym') . '-';
        $last   = static::where('ref', 'like', $prefix . '%')
            ->orderByDesc('ref')
            ->value('ref');
        $seq = $last ? ((int) substr($last, -5)) + 1 : 1;

        return $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}

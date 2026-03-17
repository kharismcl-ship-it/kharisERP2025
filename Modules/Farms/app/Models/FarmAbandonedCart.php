<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmAbandonedCart extends Model
{
    protected $table = 'farm_abandoned_carts';

    protected $fillable = [
        'company_id',
        'shop_customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'cart_data',
        'cart_total',
        'notified_at',
    ];

    protected $casts = [
        'cart_data'    => 'array',
        'cart_total'   => 'decimal:2',
        'notified_at'  => 'datetime',
    ];

    public function shopCustomer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Upsert an abandoned cart record for a logged-in customer.
     * Resets notified_at so a fresh reminder can be sent if they abandon again.
     */
    public static function saveForCustomer(ShopCustomer $customer, array $cartSession): void
    {
        if (empty($cartSession['items'])) {
            static::clearForCustomer($customer->id);
            return;
        }

        $total = round(array_sum(array_column($cartSession['items'], 'subtotal')), 2);

        static::updateOrCreate(
            ['shop_customer_id' => $customer->id],
            [
                'company_id'     => $cartSession['company_id'],
                'customer_name'  => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'cart_data'      => $cartSession['items'],
                'cart_total'     => $total,
                'notified_at'    => null, // reset so a new reminder can fire
            ]
        );
    }

    /**
     * Remove the abandoned cart record once an order is placed.
     */
    public static function clearForCustomer(int $customerId): void
    {
        static::where('shop_customer_id', $customerId)->delete();
    }
}

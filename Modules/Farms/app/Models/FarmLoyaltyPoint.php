<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmLoyaltyPoint extends Model
{
    protected $table = 'farm_loyalty_points';

    public $timestamps = false; // immutable ledger — only created_at (set via useCurrent)

    protected $fillable = [
        'company_id',
        'shop_customer_id',
        'farm_order_id',
        'points',
        'type',
        'balance_after',
        'description',
        'created_at',
    ];

    protected $casts = [
        'points'       => 'integer',
        'balance_after' => 'integer',
        'created_at'   => 'datetime',
    ];

    const TYPE_EARN       = 'earn';
    const TYPE_REDEEM     = 'redeem';
    const TYPE_ADJUSTMENT = 'adjustment';

    public function shopCustomer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(FarmOrder::class, 'farm_order_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get current loyalty balance for a customer under a company.
     */
    public static function getBalance(int $shopCustomerId, int $companyId): int
    {
        $latest = static::where('shop_customer_id', $shopCustomerId)
            ->where('company_id', $companyId)
            ->latest('created_at')
            ->value('balance_after');

        return $latest ?? 0;
    }

    /**
     * Award points to a customer. Returns the new record.
     */
    public static function award(int $shopCustomerId, int $companyId, int $points, string $description, ?int $orderId = null): static
    {
        $currentBalance = static::getBalance($shopCustomerId, $companyId);
        $newBalance     = $currentBalance + $points;

        return static::create([
            'company_id'       => $companyId,
            'shop_customer_id' => $shopCustomerId,
            'farm_order_id'    => $orderId,
            'points'           => $points,
            'type'             => self::TYPE_EARN,
            'balance_after'    => $newBalance,
            'description'      => $description,
        ]);
    }

    /**
     * Redeem points from a customer. Returns the new record or null if insufficient balance.
     */
    public static function redeem(int $shopCustomerId, int $companyId, int $points, string $description, ?int $orderId = null): ?static
    {
        $currentBalance = static::getBalance($shopCustomerId, $companyId);

        if ($currentBalance < $points) {
            return null;
        }

        return static::create([
            'company_id'       => $companyId,
            'shop_customer_id' => $shopCustomerId,
            'farm_order_id'    => $orderId,
            'points'           => -$points,
            'type'             => self::TYPE_REDEEM,
            'balance_after'    => $currentBalance - $points,
            'description'      => $description,
        ]);
    }
}

<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmProduceInventory extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_produce_inventories';

    protected $fillable = [
        'farm_id', 'crop_cycle_id', 'company_id',
        'product_name', 'unit',
        'total_quantity', 'current_stock', 'min_order_quantity', 'reserved_stock', 'sold_stock',
        'unit_cost', 'unit_price', 'market_price', 'sale_price', 'sale_starts_at', 'sale_ends_at', 'harvest_date', 'expiry_date',
        'storage_location', 'status', 'notes',
        'description', 'marketplace_listed', 'images',
    ];

    protected $casts = [
        'harvest_date'       => 'date',
        'expiry_date'        => 'date',
        'total_quantity'     => 'decimal:3',
        'current_stock'      => 'decimal:3',
        'min_order_quantity' => 'decimal:3',
        'reserved_stock'     => 'decimal:3',
        'sold_stock'         => 'decimal:3',
        'unit_cost'          => 'decimal:4',
        'unit_price'         => 'decimal:2',
        'market_price'       => 'decimal:2',
        'sale_price'         => 'decimal:2',
        'sale_starts_at'     => 'datetime',
        'sale_ends_at'       => 'datetime',
        'marketplace_listed' => 'boolean',
        'images'             => 'array',
    ];

    const STATUSES = ['in_stock', 'low_stock', 'depleted'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function priceTiers(): HasMany
    {
        return $this->hasMany(FarmPriceTier::class)->orderBy('min_quantity');
    }

    /** Is the product currently on a flash sale? */
    public function isOnSale(): bool
    {
        if (! $this->sale_price) {
            return false;
        }
        $now = now();
        if ($this->sale_ends_at && $now->gt($this->sale_ends_at)) {
            return false;
        }
        if ($this->sale_starts_at && $now->lt($this->sale_starts_at)) {
            return false;
        }
        return true;
    }

    /**
     * Effective display price (sale takes precedence over unit_price).
     * Tier pricing is applied on top of this in Show.php.
     */
    public function getEffectiveBasePrice(): float
    {
        return $this->isOnSale() ? (float) $this->sale_price : (float) $this->unit_price;
    }

    /**
     * Return the applicable tier unit price for the given quantity,
     * or null if no tier matches (caller should fall back to unit_price).
     */
    public function getApplicableTierPrice(float $qty): ?float
    {
        $applicable = null;
        foreach ($this->priceTiers as $tier) {
            if ($qty >= (float) $tier->min_quantity) {
                $applicable = (float) $tier->price_per_unit;
            }
        }
        return $applicable;
    }
}

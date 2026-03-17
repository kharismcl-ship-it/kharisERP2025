<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmBundle extends Model
{
    protected $table = 'farm_bundles';

    protected $fillable = [
        'company_id', 'name', 'description',
        'discount_percentage', 'is_active', 'images', 'sort_order',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'is_active'           => 'boolean',
        'images'              => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function bundleItems(): HasMany
    {
        return $this->hasMany(FarmBundleItem::class);
    }

    /**
     * Total retail value (sum of each item * unit_price * qty).
     */
    public function retailTotal(): float
    {
        $total = 0;
        foreach ($this->bundleItems as $item) {
            $total += (float) $item->product->getEffectiveBasePrice() * (float) $item->quantity;
        }
        return round($total, 2);
    }

    /**
     * Discounted bundle price.
     */
    public function bundlePrice(): float
    {
        return round($this->retailTotal() * (1 - (float) $this->discount_percentage / 100), 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}

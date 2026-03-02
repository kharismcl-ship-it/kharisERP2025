<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Concerns\BelongsToCompany;

class SalesCatalog extends Model
{
    use BelongsToCompany;

    protected $table = 'sales_catalogs';

    protected $fillable = [
        'company_id',
        'source_module',
        'source_type',
        'source_id',
        'sku',
        'name',
        'description',
        'unit',
        'base_price',
        'tax_rate',
        'availability_mode',
        'is_active',
    ];

    protected $casts = [
        'base_price'  => 'decimal:4',
        'tax_rate'    => 'decimal:2',
        'is_active'   => 'boolean',
        'source_id'   => 'integer',
    ];

    const SOURCE_MODULES = [
        'ManufacturingWater',
        'ManufacturingPaper',
        'Farms',
        'ProcurementInventory',
        'Fleet',
        'Construction',
        'Hostels',
        'Restaurant',
    ];

    const AVAILABILITY_MODES = ['always', 'on_request', 'stock'];

    protected static function booted(): void
    {
        static::creating(function (self $item) {
            if (empty($item->sku)) {
                $prefix     = strtoupper(substr($item->source_module, 0, 3));
                $item->sku  = $prefix . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function priceListItems(): HasMany
    {
        return $this->hasMany(SalesPriceListItem::class, 'catalog_item_id');
    }

    public function getTaxAmountAttribute(): float
    {
        return round($this->base_price * ($this->tax_rate / 100), 4);
    }

    public function getPriceIncludingTaxAttribute(): float
    {
        return round($this->base_price + $this->getTaxAmountAttribute(), 4);
    }

    /**
     * Get the override price for a given price list (falls back to base_price).
     */
    public function getPriceForList(?int $priceListId): float
    {
        if (! $priceListId) {
            return (float) $this->base_price;
        }

        $item = $this->priceListItems()->where('price_list_id', $priceListId)->first();

        return $item ? (float) $item->override_price : (float) $this->base_price;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFromModule(Builder $query, string $module): Builder
    {
        return $query->where('source_module', $module);
    }
}
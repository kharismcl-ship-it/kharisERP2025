<?php

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Concerns\BelongsToCompany;

class Item extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'items';

    protected $fillable = [
        'company_id',
        'item_category_id',
        'name',
        'sku',
        'slug',
        'description',
        'type',
        'unit_of_measure',
        'unit_price',
        'reorder_level',
        'reorder_quantity',
        'is_active',
    ];

    protected $casts = [
        'unit_price'       => 'decimal:4',
        'reorder_level'    => 'decimal:4',
        'reorder_quantity' => 'decimal:4',
        'is_active'        => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function purchaseOrderLines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function stockLevel(): HasOne
    {
        return $this->hasOne(StockLevel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('item_category_id', $categoryId);
    }

    public function scopeBySkuPattern($query, $pattern)
    {
        return $query->where('sku', 'like', $pattern);
    }
}

<?php

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'company_id',
        'item_category_id',
        'name',
        'sku',
        'slug',
    ];

    /**
     * Get the category that owns the item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    /**
     * Get the company that owns the item.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Company::class, 'company_id');
    }

    /**
     * Scope a query to only include items of a specific category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('item_category_id', $categoryId);
    }

    /**
     * Scope a query to only include items with a specific SKU pattern.
     */
    public function scopeBySkuPattern($query, $pattern)
    {
        return $query->where('sku', 'like', $pattern);
    }
}

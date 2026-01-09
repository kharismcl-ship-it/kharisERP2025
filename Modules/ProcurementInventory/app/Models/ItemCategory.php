<?php

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemCategory extends Model
{
    use HasFactory;

    protected $table = 'item_categories';

    protected $fillable = [
        'company_id',
        'name',
        'slug',
    ];

    /**
     * Get the company that owns the category.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Company::class, 'company_id');
    }

    /**
     * Get the items for the category.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'item_category_id');
    }

    /**
     * Scope a query to only include categories for a specific company.
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}

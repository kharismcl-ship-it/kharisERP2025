<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorCatalog extends Model
{
    use HasFactory;

    protected $table = 'procurement_vendor_catalogs';

    protected $fillable = [
        'company_id',
        'vendor_id',
        'name',
        'description',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to'   => 'date',
        'is_active'      => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function catalogItems(): HasMany
    {
        return $this->hasMany(VendorCatalogItem::class, 'catalog_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('effective_from', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now()->toDateString());
            });
    }

    public static function getBestPrice(int $companyId, int $itemId): ?VendorCatalogItem
    {
        return VendorCatalogItem::whereHas('catalog', function ($q) use ($companyId) {
            $q->where('company_id', $companyId)
                ->where('is_active', true)
                ->where('effective_from', '<=', now()->toDateString())
                ->where(function ($q2) {
                    $q2->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', now()->toDateString());
                });
        })
            ->where('item_id', $itemId)
            ->orderBy('unit_price')
            ->first();
    }
}
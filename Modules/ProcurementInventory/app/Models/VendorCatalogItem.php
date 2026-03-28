<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorCatalogItem extends Model
{
    use HasFactory;

    protected $table = 'procurement_vendor_catalog_items';

    protected $fillable = [
        'catalog_id',
        'item_id',
        'vendor_sku',
        'unit_price',
        'min_order_quantity',
        'lead_time_days',
        'notes',
    ];

    protected $casts = [
        'unit_price'         => 'decimal:4',
        'min_order_quantity' => 'decimal:4',
        'lead_time_days'     => 'integer',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(VendorCatalog::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
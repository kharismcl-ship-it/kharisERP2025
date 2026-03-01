<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class FarmProduceInventory extends Model
{
    protected $table = 'farm_produce_inventories';

    protected $fillable = [
        'farm_id', 'crop_cycle_id', 'company_id',
        'product_name', 'unit',
        'total_quantity', 'current_stock', 'reserved_stock', 'sold_stock',
        'unit_cost', 'harvest_date', 'expiry_date',
        'storage_location', 'status', 'notes',
    ];

    protected $casts = [
        'harvest_date'    => 'date',
        'expiry_date'     => 'date',
        'total_quantity'  => 'decimal:3',
        'current_stock'   => 'decimal:3',
        'reserved_stock'  => 'decimal:3',
        'sold_stock'      => 'decimal:3',
        'unit_cost'       => 'decimal:4',
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
}

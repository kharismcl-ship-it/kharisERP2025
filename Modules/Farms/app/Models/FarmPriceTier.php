<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmPriceTier extends Model
{
    protected $table = 'farm_price_tiers';

    protected $fillable = [
        'farm_produce_inventory_id',
        'company_id',
        'min_quantity',
        'price_per_unit',
        'label',
    ];

    protected $casts = [
        'min_quantity'   => 'decimal:3',
        'price_per_unit' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(FarmProduceInventory::class, 'farm_produce_inventory_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

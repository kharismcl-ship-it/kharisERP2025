<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmSavedAddress extends Model
{
    protected $table = 'farm_saved_addresses';

    protected $fillable = [
        'shop_customer_id', 'label', 'address', 'landmark', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function shopCustomer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class);
    }
}

<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmCustomerWishlist extends Model
{
    protected $table = 'farm_customer_wishlists';

    protected $fillable = [
        'shop_customer_id',
        'farm_produce_inventory_id',
    ];

    public function shopCustomer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(FarmProduceInventory::class, 'farm_produce_inventory_id');
    }
}

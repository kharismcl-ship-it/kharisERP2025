<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmRestockNotification extends Model
{
    protected $table = 'farm_restock_notifications';

    protected $fillable = [
        'company_id',
        'farm_produce_inventory_id',
        'shop_customer_id',
        'email',
        'phone',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(FarmProduceInventory::class, 'farm_produce_inventory_id');
    }

    public function shopCustomer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** Pending notifications that haven't been sent yet */
    public function scopePending($query)
    {
        return $query->whereNull('notified_at');
    }
}

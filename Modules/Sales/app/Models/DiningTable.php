<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningTable extends Model
{
    protected $fillable = [
        'restaurant_id',
        'section',
        'table_number',
        'capacity',
        'status',
    ];

    const STATUSES = ['available', 'occupied', 'reserved', 'cleaning'];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(SalesRestaurant::class, 'restaurant_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(DiningOrder::class, 'table_id');
    }

    public function activeOrder(): ?DiningOrder
    {
        return $this->orders()->whereNotIn('status', ['paid', 'cancelled'])->latest()->first();
    }
}
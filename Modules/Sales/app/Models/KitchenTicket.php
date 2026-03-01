<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenTicket extends Model
{
    protected $fillable = [
        'dining_order_id',
        'station',
        'status',
        'fired_at',
        'completed_at',
    ];

    protected $casts = [
        'fired_at'     => 'datetime',
        'completed_at' => 'datetime',
    ];

    const STATUSES = ['pending', 'in_progress', 'completed'];

    public function diningOrder(): BelongsTo
    {
        return $this->belongsTo(DiningOrder::class, 'dining_order_id');
    }
}
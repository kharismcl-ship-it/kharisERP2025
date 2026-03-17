<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Modules\Fleet\Models\TripLog;

class FarmOrderDelivery extends Model
{
    protected $table = 'farm_order_deliveries';

    protected $fillable = [
        'farm_order_id', 'trip_log_id', 'vehicle_id', 'driver_user_id',
        'estimated_delivery_at', 'delivered_at', 'status', 'notes',
    ];

    protected $casts = [
        'estimated_delivery_at' => 'datetime',
        'delivered_at'          => 'datetime',
    ];

    const STATUSES = ['pending_dispatch', 'dispatched', 'out_for_delivery', 'delivered', 'failed'];

    public function farmOrder(): BelongsTo
    {
        return $this->belongsTo(FarmOrder::class);
    }

    public function tripLog(): BelongsTo
    {
        return $this->belongsTo(TripLog::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_user_id');
    }
}

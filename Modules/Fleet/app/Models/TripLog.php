<?php

namespace Modules\Fleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\User;

class TripLog extends Model
{
    protected $fillable = [
        'vehicle_id',
        'company_id',
        'driver_id',
        'trip_reference',
        'trip_date',
        'origin',
        'destination',
        'purpose',
        'start_mileage',
        'end_mileage',
        'distance_km',
        'departure_time',
        'return_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'trip_date'    => 'date',
        'start_mileage'=> 'decimal:2',
        'end_mileage'  => 'decimal:2',
        'distance_km'  => 'decimal:2',
    ];

    const STATUSES = ['planned', 'in_progress', 'completed', 'cancelled'];

    protected static function booted(): void
    {
        static::creating(function (self $trip) {
            if (empty($trip->trip_reference)) {
                $trip->trip_reference = 'TRIP-' . now()->format('Ym') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });

        static::saving(function (self $trip) {
            if ($trip->start_mileage && $trip->end_mileage && $trip->end_mileage > $trip->start_mileage) {
                $trip->distance_km = round($trip->end_mileage - $trip->start_mileage, 2);
            }
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}

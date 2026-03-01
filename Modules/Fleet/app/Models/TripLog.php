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
        'fare_amount',
        'client_name',
        'client_phone',
        'client_email',
    ];

    protected $casts = [
        'trip_date'    => 'date',
        'start_mileage'=> 'decimal:2',
        'end_mileage'  => 'decimal:2',
        'distance_km'  => 'decimal:2',
        'fare_amount'  => 'decimal:2',
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

            // Odometer validation: start mileage cannot be less than vehicle's current mileage
            if ($trip->start_mileage && $trip->vehicle_id) {
                $vehicle = Vehicle::find($trip->vehicle_id);
                if ($vehicle && $trip->start_mileage < $vehicle->current_mileage) {
                    throw new \InvalidArgumentException(
                        "Start mileage ({$trip->start_mileage} km) cannot be less than the vehicle's current mileage ({$vehicle->current_mileage} km)."
                    );
                }
            }

            // end_mileage must be >= start_mileage when provided
            if ($trip->end_mileage && $trip->start_mileage && $trip->end_mileage < $trip->start_mileage) {
                throw new \InvalidArgumentException(
                    "End mileage ({$trip->end_mileage} km) cannot be less than start mileage ({$trip->start_mileage} km)."
                );
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

<?php

namespace Modules\Fleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\User;

class FuelLog extends Model
{
    protected $fillable = [
        'vehicle_id',
        'company_id',
        'driver_id',
        'fill_date',
        'litres',
        'price_per_litre',
        'total_cost',
        'mileage_at_fill',
        'fuel_station',
        'notes',
    ];

    protected $casts = [
        'fill_date'       => 'date',
        'litres'          => 'decimal:3',
        'price_per_litre' => 'decimal:4',
        'total_cost'      => 'decimal:2',
        'mileage_at_fill' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $log) {
            // Auto-calculate total cost
            if ($log->litres && $log->price_per_litre && ! $log->isDirty('total_cost')) {
                $log->total_cost = round($log->litres * $log->price_per_litre, 2);
            }

            // Odometer validation: mileage at fill cannot be less than vehicle's current mileage
            if ($log->mileage_at_fill && $log->vehicle_id) {
                $vehicle = Vehicle::find($log->vehicle_id);
                if ($vehicle && $log->mileage_at_fill < $vehicle->current_mileage) {
                    throw new \InvalidArgumentException(
                        "Mileage at fill ({$log->mileage_at_fill} km) cannot be less than the vehicle's current mileage ({$vehicle->current_mileage} km)."
                    );
                }
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

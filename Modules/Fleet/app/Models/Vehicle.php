<?php

namespace Modules\Fleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Models\Company;

class Vehicle extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'make',
        'model',
        'year',
        'type',
        'color',
        'plate',
        'chassis_number',
        'engine_number',
        'fuel_type',
        'capacity',
        'current_mileage',
        'status',
        'purchase_date',
        'purchase_price',
    ];

    protected $casts = [
        'year'           => 'integer',
        'capacity'       => 'decimal:2',
        'current_mileage'=> 'decimal:2',
        'purchase_date'  => 'date',
        'purchase_price' => 'decimal:2',
    ];

    const TYPES = ['car', 'truck', 'van', 'bus', 'motorcycle', 'other'];
    const FUEL_TYPES = ['petrol', 'diesel', 'electric', 'hybrid', 'lpg'];
    const STATUSES = ['active', 'inactive', 'under_maintenance', 'retired'];

    protected static function booted(): void
    {
        static::creating(function (self $vehicle) {
            if (empty($vehicle->slug)) {
                $vehicle->slug = Str::slug($vehicle->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    public function driverAssignments(): HasMany
    {
        return $this->hasMany(DriverAssignment::class);
    }

    public function currentDriver(): HasOne
    {
        return $this->hasOne(DriverAssignment::class)
            ->where('is_primary', true)
            ->whereNull('assigned_until')
            ->latest('assigned_from');
    }

    public function tripLogs(): HasMany
    {
        return $this->hasMany(TripLog::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getDisplayNameAttribute(): string
    {
        return trim("{$this->year} {$this->make} {$this->model} – {$this->plate}");
    }
}

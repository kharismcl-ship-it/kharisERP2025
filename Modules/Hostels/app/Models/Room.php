<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Database\factories\RoomFactory;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_id',
        'block_id',
        'floor_id',
        'room_number',
        'type',
        'gender_policy',
        'base_rate',
        'per_night_rate',
        'per_semester_rate',
        'per_year_rate',
        'billing_cycle',
        'max_occupancy',
        'current_occupancy',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'base_rate' => 'float',
        'per_night_rate' => 'float',
        'per_semester_rate' => 'float',
        'per_year_rate' => 'float',
    ];

    protected static function newFactory(): RoomFactory
    {
        return RoomFactory::new();
    }

    // Accessor to maintain compatibility with existing code
    public function getRoomTypeAttribute()
    {
        return $this->type;
    }

    // Mutator to maintain compatibility with existing code
    public function setRoomTypeAttribute($value)
    {
        $this->attributes['type'] = $value;
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function block()
    {
        return $this->belongsTo(HostelBlock::class);
    }

    public function floor()
    {
        return $this->belongsTo(HostelFloor::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * Get the rate for a specific billing cycle.
     */
    public function getRateForBillingCycle(?string $billingCycle): float
    {
        if (! $billingCycle) {
            return $this->base_rate ?? 0;
        }

        return match ($billingCycle) {
            'short_stay' => $this->per_night_rate ?? $this->base_rate ?? 0,
            'semester' => $this->per_semester_rate ?? $this->base_rate ?? 0,
            'academic' => $this->per_year_rate ?? $this->base_rate ?? 0,
            'per_night' => $this->per_night_rate ?? $this->base_rate ?? 0,
            'per_semester' => $this->per_semester_rate ?? $this->base_rate ?? 0,
            'per_year' => $this->per_year_rate ?? $this->base_rate ?? 0,
            default => $this->base_rate ?? 0
        };
    }

    /**
     * Get the inventory assignments for the room.
     */
    public function inventoryAssignments()
    {
        return $this->hasMany(RoomInventoryAssignment::class);
    }

    /**
     * Get the active inventory assignments for the room.
     */
    public function activeInventoryAssignments()
    {
        return $this->inventoryAssignments()->active();
    }
}

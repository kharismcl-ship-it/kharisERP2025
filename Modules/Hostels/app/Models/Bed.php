<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Database\factories\BedFactory;

/**
 * Bed Model
 *
 * Represents a bed in a hostel room.
 *
 * Concurrency Control:
 * - When booking a bed, always use database transactions with row-level locking
 * - Check bed status with `lockForUpdate()` before updating
 * - Ensure no active bookings exist for the bed before allowing a new booking
 */
class Bed extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'room_id',
        'bed_number',
        'status',
        'is_upper_bunk',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_upper_bunk' => 'boolean',
    ];

    protected static function newFactory(): BedFactory
    {
        return BedFactory::new();
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}

<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Database\factories\BookingChangeRequestFactory;

class BookingChangeRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'requested_room_id',
        'requested_bed_id',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the booking associated with the change request.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the requested room.
     */
    public function requestedRoom()
    {
        return $this->belongsTo(Room::class, 'requested_room_id');
    }

    /**
     * Get the requested bed.
     */
    public function requestedBed()
    {
        return $this->belongsTo(Bed::class, 'requested_bed_id');
    }

    /**
     * Get the user who approved the request.
     */
    public function approvedBy()
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'approved_by');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    protected static function newFactory(): BookingChangeRequestFactory
    {
        return BookingChangeRequestFactory::new();
    }
}

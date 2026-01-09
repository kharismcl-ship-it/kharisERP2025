<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * BookingCancellationPolicy Model
 *
 * Defines cancellation rules and refund policies for hostel bookings
 */
class BookingCancellationPolicy extends Model
{
    protected $table = 'booking_cancellation_policies';

    protected $fillable = [
        'hostel_id',
        'name',
        'description',
        'cancellation_window_hours',
        'refund_percentage',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'cancellation_window_hours' => 'integer',
        'refund_percentage' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Check if cancellation is allowed based on check-in time
     */
    public function isCancellationAllowed(\DateTimeInterface $checkInDate): bool
    {
        $cutoffTime = (clone $checkInDate)->modify("-{$this->cancellation_window_hours} hours");

        return now() <= $cutoffTime;
    }

    /**
     * Calculate refund amount based on policy
     */
    public function calculateRefundAmount(float $totalAmount, float $amountPaid): float
    {
        return min($amountPaid, $totalAmount * $this->refund_percentage / 100);
    }
}

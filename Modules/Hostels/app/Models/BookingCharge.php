<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCharge extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_booking_charges';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'fee_type_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'amount' => 'float',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
}

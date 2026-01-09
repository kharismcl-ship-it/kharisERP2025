<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_fee_types';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_id',
        'name',
        'code',
        'default_amount',
        'billing_cycle',
        'is_mandatory',
        'is_active',
    ];

    protected $casts = [
        'default_amount' => 'float',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function bookingCharges()
    {
        return $this->hasMany(BookingCharge::class);
    }

    public function hostelFeeSettings()
    {
        return $this->hasMany(HostelFeeSetting::class);
    }
}

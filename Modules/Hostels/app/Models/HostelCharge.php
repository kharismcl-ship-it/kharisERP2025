<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Database\factories\HostelChargeFactory;

class HostelCharge extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hostel_charges';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hostel_id',
        'fee_type_id',
        'name',
        'charge_type',
        'amount',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    protected static function newFactory(): HostelChargeFactory
    {
        return HostelChargeFactory::new();
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
}

<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PaymentsChannel\Traits\HasPayments;

class HostelBookOrder extends Model
{
    use HasFactory, HasPayments;

    protected $fillable = [
        'company_id',
        'hostel_occupant_id',
        'hostel_id',
        'reference',
        'subtotal',
        'total',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at'  => 'datetime',
        'subtotal' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->reference) {
                $prefix = 'BK-'.now()->format('Ym').'-';
                $last = static::where('reference', 'like', $prefix.'%')->max('reference');
                $seq = $last ? ((int) substr($last, -5) + 1) : 1;
                $model->reference = $prefix.str_pad($seq, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function occupant()
    {
        return $this->belongsTo(HostelOccupant::class, 'hostel_occupant_id');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function items()
    {
        return $this->hasMany(HostelBookOrderItem::class);
    }

    // HasPayments required helpers

    public function getPaymentDescription(): ?string
    {
        return 'Book Order: '.$this->reference;
    }

    public function getPaymentAmount(): float
    {
        return (float) $this->total;
    }

    public function getPaymentCurrency(): string
    {
        return 'GHS';
    }

    public function getPaymentCustomerName(): ?string
    {
        return $this->occupant?->full_name;
    }

    public function getPaymentCustomerEmail(): ?string
    {
        return $this->occupant?->email;
    }

    public function getPaymentCustomerPhone(): ?string
    {
        return $this->occupant?->phone;
    }
}

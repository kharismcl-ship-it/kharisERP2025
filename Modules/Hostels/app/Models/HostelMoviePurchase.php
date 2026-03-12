<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PaymentsChannel\Traits\HasPayments;

class HostelMoviePurchase extends Model
{
    use HasFactory, HasPayments;

    protected $fillable = [
        'hostel_movie_id',
        'hostel_occupant_id',
        'amount_paid',
        'paid_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'paid_at'     => 'datetime',
        'expires_at'  => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function movie()
    {
        return $this->belongsTo(HostelMovie::class, 'hostel_movie_id');
    }

    public function occupant()
    {
        return $this->belongsTo(HostelOccupant::class, 'hostel_occupant_id');
    }

    // HasPayments required helpers

    public function getPaymentDescription(): ?string
    {
        return 'Movie: '.$this->movie?->title;
    }

    public function getPaymentAmount(): float
    {
        return (float) $this->amount_paid;
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

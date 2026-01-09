<?php

namespace Modules\PaymentsChannel\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayIntent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'provider',
        'pay_method_id',
        'payable_type',
        'payable_id',
        'reference',
        'provider_reference',
        'amount',
        'currency',
        'status',
        'description',
        'customer_name',
        'customer_email',
        'customer_phone',
        'return_url',
        'callback_url',
        'metadata',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the payable entity that owns this payment intent.
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Get the company that owns this payment intent.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the payment method used for this intent.
     */
    public function payMethod()
    {
        return $this->belongsTo(PayMethod::class, 'pay_method_id');
    }

    /**
     * Get the transactions for this payment intent.
     */
    public function transactions()
    {
        return $this->hasMany(PayTransaction::class, 'pay_intent_id');
    }
}

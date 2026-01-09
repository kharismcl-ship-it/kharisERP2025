<?php

namespace Modules\PaymentsChannel\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pay_intent_id',
        'company_id',
        'provider',
        'transaction_type',
        'amount',
        'currency',
        'provider_transaction_id',
        'status',
        'raw_payload',
        'processed_at',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'raw_payload' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the payment intent that owns this transaction.
     */
    public function payIntent()
    {
        return $this->belongsTo(PayIntent::class, 'pay_intent_id');
    }

    /**
     * Get the company that owns this transaction.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

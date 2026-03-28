<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentBatchItem extends Model
{
    use HasFactory;

    protected $table = 'fin_payment_batch_items';

    protected $fillable = [
        'batch_id',
        'invoice_id',
        'amount',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(PaymentBatch::class, 'batch_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
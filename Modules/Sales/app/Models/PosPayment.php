<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosPayment extends Model
{
    protected $fillable = [
        'pos_sale_id',
        'method',
        'amount',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    const METHODS = ['cash', 'momo', 'card', 'credit', 'voucher'];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }
}
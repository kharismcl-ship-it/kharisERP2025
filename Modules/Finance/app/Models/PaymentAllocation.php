<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class PaymentAllocation extends Model
{
    protected $table = 'fin_payment_allocations';

    protected $fillable = [
        'company_id',
        'payment_id',
        'invoice_id',
        'amount',
        'allocated_at',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'allocated_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
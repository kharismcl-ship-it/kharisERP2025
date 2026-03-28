<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmGrowerPayment extends Model
{
    protected $table = 'farm_grower_payments';

    protected $fillable = [
        'company_id',
        'farm_cooperative_id',
        'farm_id',
        'payment_ref',
        'payment_type',
        'harvest_record_id',
        'quantity_kg',
        'price_per_kg',
        'gross_amount',
        'deductions',
        'net_amount',
        'payment_method',
        'momo_number',
        'payment_date',
        'status',
        'paid_by',
        'notes',
    ];

    protected $casts = [
        'deductions'   => 'array',
        'payment_date' => 'date',
        'quantity_kg'  => 'float',
        'price_per_kg' => 'float',
        'gross_amount' => 'float',
        'net_amount'   => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $payment) {
            if (empty($payment->payment_ref)) {
                $payment->payment_ref = self::generateRef();
            }
        });
    }

    public static function generateRef(): string
    {
        $prefix = 'GRP-' . now()->format('Ym') . '-';
        $last = self::where('payment_ref', 'like', $prefix . '%')
            ->orderByDesc('payment_ref')
            ->value('payment_ref');

        $next = $last ? (int) substr($last, -5) + 1 : 1;

        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(FarmCooperative::class, 'farm_cooperative_id');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function harvestRecord(): BelongsTo
    {
        return $this->belongsTo(HarvestRecord::class);
    }
}
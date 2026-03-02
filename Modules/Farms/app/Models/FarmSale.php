<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Farms\Events\FarmSaleCreated;
use Modules\PaymentsChannel\Traits\HasPayments;
use App\Models\Concerns\BelongsToCompany;

class FarmSale extends Model
{
    use HasPayments, BelongsToCompany;

    protected $table = 'farm_sales';

    protected $fillable = [
        'farm_id', 'crop_cycle_id', 'livestock_batch_id', 'invoice_id', 'company_id',
        'sale_date', 'product_name', 'product_type',
        'quantity', 'unit', 'unit_price', 'total_amount',
        'buyer_name', 'buyer_contact', 'payment_status', 'notes',
    ];

    protected $casts = [
        'sale_date'    => 'date',
        'quantity'     => 'decimal:3',
        'unit_price'   => 'decimal:4',
        'total_amount' => 'decimal:2',
    ];

    const PRODUCT_TYPES = ['crop', 'livestock', 'processed', 'other'];

    const PAYMENT_STATUSES = ['pending', 'partial', 'paid'];

    protected static function booted(): void
    {
        static::saving(function (self $sale) {
            if (! $sale->isDirty('total_amount')) {
                $sale->total_amount = round($sale->quantity * $sale->unit_price, 2);
            }
        });

        static::created(function (self $sale) {
            FarmSaleCreated::dispatch($sale);
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function livestockBatch(): BelongsTo
    {
        return $this->belongsTo(LivestockBatch::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(\Modules\Finance\Models\Invoice::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    // HasPayments overrides
    public function getPaymentDescription(): string
    {
        return "Farm Sale: {$this->product_name} ({$this->quantity} {$this->unit}) — {$this->farm?->name}";
    }

    public function getPaymentAmount(): float
    {
        return (float) $this->total_amount;
    }

    public function getPaymentCurrency(): string
    {
        return 'GHS';
    }

    public function getPaymentCustomerName(): ?string
    {
        return $this->buyer_name;
    }

    public function getPaymentCustomerPhone(): ?string
    {
        return $this->buyer_contact;
    }
}
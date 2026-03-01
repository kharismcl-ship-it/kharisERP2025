<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Sales\Events\SalesOrderConfirmed;
use Modules\Sales\Events\SalesOrderFulfilled;

class SalesOrder extends Model
{
    protected $fillable = [
        'company_id',
        'quotation_id',
        'contact_id',
        'organization_id',
        'reference',
        'status',
        'subtotal',
        'tax',
        'total',
        'invoice_id',
        'notes',
        'confirmed_at',
        'fulfilled_at',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'tax'          => 'decimal:2',
        'total'        => 'decimal:2',
        'confirmed_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    const STATUSES = ['pending', 'confirmed', 'processing', 'fulfilled', 'cancelled'];

    protected static function booted(): void
    {
        static::creating(function (self $o) {
            if (empty($o->reference)) {
                $prefix   = 'SO-' . now()->format('Ym');
                $last     = static::where('company_id', $o->company_id)
                    ->where('reference', 'like', $prefix . '-%')
                    ->orderByDesc('id')
                    ->value('reference');
                $seq      = $last ? (int) substr($last, -5) + 1 : 1;
                $o->reference = $prefix . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
            }
        });

        static::updated(function (self $o) {
            if ($o->wasChanged('status')) {
                if ($o->status === 'confirmed') {
                    $o->updateQuietly(['confirmed_at' => now()]);
                    SalesOrderConfirmed::dispatch($o);
                }
                if ($o->status === 'fulfilled') {
                    $o->updateQuietly(['fulfilled_at' => now()]);
                    SalesOrderFulfilled::dispatch($o);
                }
            }
        });
    }

    public function recalculate(): void
    {
        $this->subtotal = $this->lines->sum('line_total');
        $taxRate        = config('sales.default_tax_rate', 15.0) / 100;
        $this->tax      = round($this->subtotal * $taxRate, 2);
        $this->total    = round($this->subtotal + $this->tax, 2);
        $this->saveQuietly();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(SalesQuotation::class, 'quotation_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(SalesContact::class, 'contact_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(SalesOrganization::class, 'organization_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SalesOrderLine::class, 'order_id');
    }
}
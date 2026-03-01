<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Sales\Events\PosSaleCompleted;

class PosSale extends Model
{
    protected $fillable = [
        'session_id',
        'contact_id',
        'reference',
        'subtotal',
        'tax',
        'total',
        'invoice_id',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax'      => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $sale) {
            if (empty($sale->reference)) {
                $prefix = 'POS-' . now()->format('Ymd');
                $last   = static::whereHas('session', fn ($q) => $q->where('terminal_id', optional($sale->session)->terminal_id))
                    ->where('reference', 'like', $prefix . '-%')
                    ->orderByDesc('id')
                    ->value('reference');
                $seq    = $last ? (int) substr($last, -5) + 1 : 1;
                $sale->reference = $prefix . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
            }
        });

        static::created(function (self $sale) {
            PosSaleCompleted::dispatch($sale);
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

    public function session(): BelongsTo
    {
        return $this->belongsTo(PosSession::class, 'session_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(SalesContact::class, 'contact_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PosSaleLine::class, 'pos_sale_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PosPayment::class, 'pos_sale_id');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }
}
<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Sales\Events\QuotationSent;
use App\Models\Concerns\BelongsToCompany;

class SalesQuotation extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'contact_id',
        'organization_id',
        'reference',
        'status',
        'valid_until',
        'subtotal',
        'tax',
        'total',
        'notes',
        'sent_at',
        'accepted_at',
    ];

    protected $casts = [
        'valid_until'  => 'date',
        'subtotal'     => 'decimal:2',
        'tax'          => 'decimal:2',
        'total'        => 'decimal:2',
        'sent_at'      => 'datetime',
        'accepted_at'  => 'datetime',
    ];

    const STATUSES = ['draft', 'sent', 'accepted', 'rejected', 'expired'];

    protected static function booted(): void
    {
        static::creating(function (self $q) {
            if (empty($q->reference)) {
                $prefix     = 'QUO-' . now()->format('Ym');
                $last       = static::where('company_id', $q->company_id)
                    ->where('reference', 'like', $prefix . '-%')
                    ->orderByDesc('id')
                    ->value('reference');
                $seq        = $last ? (int) substr($last, -5) + 1 : 1;
                $q->reference = $prefix . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
            }
            if (empty($q->valid_until)) {
                $q->valid_until = now()->addDays(config('sales.quotation_validity_days', 30))->toDateString();
            }
        });

        static::updated(function (self $q) {
            if ($q->wasChanged('status') && $q->status === 'sent') {
                QuotationSent::dispatch($q);
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
        return $this->hasMany(SalesQuotationLine::class, 'quotation_id');
    }
}
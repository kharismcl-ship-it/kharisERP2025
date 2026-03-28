<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    protected $table = 'fin_advance_payments';

    protected $fillable = [
        'company_id',
        'advance_number',
        'advance_type',
        'party_name',
        'party_id',
        'party_type',
        'amount',
        'currency',
        'received_date',
        'applied_amount',
        'status',
        'payment_method',
        'reference',
        'gl_account_id',
        'notes',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'applied_amount' => 'decimal:2',
        'received_date'  => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $advance) {
            if (empty($advance->advance_number)) {
                $prefix = 'ADV-' . now()->format('Ym') . '-';
                $last   = static::where('advance_number', 'like', $prefix . '%')
                    ->orderByDesc('id')
                    ->first();
                $seq    = $last ? ((int) substr($last->advance_number, -5)) + 1 : 1;
                $advance->advance_number = $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function remainingBalance(): float
    {
        return max(0.0, (float) $this->amount - (float) $this->applied_amount);
    }

    public function isFullyApplied(): bool
    {
        return $this->remainingBalance() <= 0.01;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(Account::class, 'gl_account_id');
    }
}
<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankReconciliation extends Model
{
    protected $fillable = [
        'company_id',
        'bank_account_id',
        'statement_date',
        'statement_balance',
        'book_balance',
        'difference',
        'status',
        'reconciled_by',
        'reconciled_at',
        'notes',
    ];

    protected $casts = [
        'statement_date'    => 'date',
        'statement_balance' => 'decimal:2',
        'book_balance'      => 'decimal:2',
        'difference'        => 'decimal:2',
        'reconciled_at'     => 'datetime',
    ];

    public const STATUSES = [
        'draft'       => 'Draft',
        'reconciled'  => 'Reconciled',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reconciled_by');
    }

    public function isReconciled(): bool
    {
        return $this->status === 'reconciled';
    }

    /** Complete the reconciliation */
    public function complete(int $userId): void
    {
        $this->update([
            'status'         => 'reconciled',
            'reconciled_by'  => $userId,
            'reconciled_at'  => now(),
        ]);
    }
}

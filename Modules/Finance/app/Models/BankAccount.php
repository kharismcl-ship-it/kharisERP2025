<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class BankAccount extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'gl_account_id',
        'name',
        'bank_name',
        'account_number',
        'branch',
        'currency',
        'opening_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'gl_account_id');
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(BankReconciliation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Current book balance = opening + all payments received to this account */
    public function bookBalance(): float
    {
        if (! $this->gl_account_id) {
            return (float) $this->opening_balance;
        }

        $debits  = JournalLine::where('account_id', $this->gl_account_id)->sum('debit');
        $credits = JournalLine::where('account_id', $this->gl_account_id)->sum('credit');

        return (float) $this->opening_balance + $debits - $credits;
    }
}

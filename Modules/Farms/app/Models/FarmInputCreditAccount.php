<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmInputCreditAccount extends Model
{
    protected $table = 'farm_input_credit_accounts';

    protected $fillable = [
        'company_id', 'farm_id', 'account_ref', 'farmer_name', 'farmer_phone',
        'scheme_name', 'scheme_type', 'credit_limit', 'amount_drawn', 'amount_repaid',
        'season_start', 'repayment_due_date', 'status', 'notes',
    ];

    protected $casts = [
        'season_start'       => 'date',
        'repayment_due_date' => 'date',
        'credit_limit'       => 'float',
        'amount_drawn'       => 'float',
        'amount_repaid'      => 'float',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(FarmInputVoucher::class, 'farm_input_credit_account_id');
    }

    public function availableBalance(): float
    {
        return $this->credit_limit - $this->amount_drawn;
    }

    public function outstandingBalance(): float
    {
        return $this->amount_drawn - $this->amount_repaid;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $model) {
            if (empty($model->account_ref)) {
                $yearMonth = now()->format('Ym');
                $count = static::where('company_id', $model->company_id)
                    ->whereRaw('account_ref LIKE ?', ["ICA-{$yearMonth}-%"])
                    ->count() + 1;
                $model->account_ref = "ICA-{$yearMonth}-" . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
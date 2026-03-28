<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{
    use HasFactory;

    protected $table = 'fin_budget_lines';

    protected $fillable = [
        'budget_id',
        'account_id',
        'cost_centre_id',
        'description',
        'jan', 'feb', 'mar', 'apr', 'may', 'jun',
        'jul', 'aug', 'sep', 'oct', 'nov', 'dec',
        'annual_total',
    ];

    protected $casts = [
        'jan' => 'decimal:2', 'feb' => 'decimal:2', 'mar' => 'decimal:2',
        'apr' => 'decimal:2', 'may' => 'decimal:2', 'jun' => 'decimal:2',
        'jul' => 'decimal:2', 'aug' => 'decimal:2', 'sep' => 'decimal:2',
        'oct' => 'decimal:2', 'nov' => 'decimal:2', 'dec' => 'decimal:2',
        'annual_total' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $model) {
            $model->annual_total = collect(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'])
                ->sum(fn ($m) => (float) ($model->$m ?? 0));
        });
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class, 'cost_centre_id');
    }
}
<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseClaimLine extends Model
{
    use HasFactory;

    protected $table = 'fin_expense_claim_lines';

    protected $fillable = [
        'claim_id',
        'expense_category_id',
        'description',
        'expense_date',
        'amount',
        'receipt_path',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function claim()
    {
        return $this->belongsTo(ExpenseClaim::class, 'claim_id');
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
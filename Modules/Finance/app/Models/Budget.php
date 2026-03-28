<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $table = 'fin_budgets';

    protected $fillable = [
        'company_id',
        'name',
        'budget_year',
        'period_type',
        'status',
        'total_budget',
        'approved_by_user_id',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'budget_year'  => 'integer',
        'total_budget' => 'decimal:2',
        'approved_at'  => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function lines()
    {
        return $this->hasMany(BudgetLine::class, 'budget_id');
    }
}
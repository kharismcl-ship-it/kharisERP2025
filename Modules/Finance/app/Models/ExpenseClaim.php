<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class ExpenseClaim extends Model
{
    use HasFactory;

    protected $table = 'fin_expense_claims';

    protected $fillable = [
        'company_id',
        'employee_id',
        'claim_number',
        'claim_date',
        'purpose',
        'total',
        'status',
        'submitted_at',
        'approved_by_user_id',
        'approved_at',
        'rejection_reason',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'claim_date'   => 'date',
        'payment_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
        'total'        => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->claim_number)) {
                $count = static::where('company_id', $model->company_id)->count() + 1;
                $model->claim_number = 'EXP-' . now()->format('Ym') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function lines()
    {
        return $this->hasMany(ExpenseClaimLine::class, 'claim_id');
    }
}
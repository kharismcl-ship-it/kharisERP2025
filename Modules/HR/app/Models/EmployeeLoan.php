<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\User;

class EmployeeLoan extends Model
{
    protected $table = 'hr_employee_loans';

    protected $fillable = [
        'company_id', 'employee_id', 'loan_type', 'principal_amount', 'outstanding_balance',
        'monthly_deduction', 'approved_date', 'start_date', 'expected_end_date',
        'repayment_months', 'status', 'purpose', 'rejection_reason', 'approved_by',
    ];

    protected $casts = [
        'principal_amount'   => 'decimal:2',
        'outstanding_balance'=> 'decimal:2',
        'monthly_deduction'  => 'decimal:2',
        'approved_date'      => 'date',
        'start_date'         => 'date',
        'expected_end_date'  => 'date',
        'repayment_months'   => 'integer',
    ];

    const LOAN_TYPES = ['salary_advance' => 'Salary Advance', 'personal_loan' => 'Personal Loan', 'emergency_loan' => 'Emergency Loan'];
    const STATUSES   = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'active' => 'Active', 'cleared' => 'Cleared'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class, 'employee_loan_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
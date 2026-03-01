<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRepayment extends Model
{
    protected $table = 'hr_loan_repayments';

    protected $fillable = [
        'employee_loan_id', 'payment_date', 'amount', 'outstanding_before',
        'outstanding_after', 'payment_method', 'payroll_run_id', 'notes',
    ];

    protected $casts = [
        'payment_date'      => 'date',
        'amount'            => 'decimal:2',
        'outstanding_before'=> 'decimal:2',
        'outstanding_after' => 'decimal:2',
    ];

    const PAYMENT_METHODS = ['payroll_deduction' => 'Payroll Deduction', 'bank_transfer' => 'Bank Transfer', 'cash' => 'Cash'];

    public function employeeLoan(): BelongsTo
    {
        return $this->belongsTo(EmployeeLoan::class);
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }
}

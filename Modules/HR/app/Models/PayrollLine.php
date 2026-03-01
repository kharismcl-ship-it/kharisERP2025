<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollLine extends Model
{
    protected $table = 'hr_payroll_lines';

    protected $fillable = [
        'payroll_run_id', 'employee_id', 'basic_salary', 'allowances',
        'deductions', 'gross_salary', 'total_allowances', 'total_deductions',
        'paye_tax', 'ssnit_employee', 'ssnit_employer', 'net_salary', 'notes',
    ];

    protected $casts = [
        'allowances'       => 'array',
        'deductions'       => 'array',
        'basic_salary'     => 'decimal:2',
        'gross_salary'     => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'paye_tax'         => 'decimal:2',
        'ssnit_employee'   => 'decimal:2',
        'ssnit_employer'   => 'decimal:2',
        'net_salary'       => 'decimal:2',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
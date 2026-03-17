<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\User;
use App\Models\Concerns\BelongsToCompany;

class PayrollRun extends Model
{
    use BelongsToCompany;

    protected $table = 'hr_payroll_runs';

    protected $fillable = [
        'company_id', 'period_year', 'period_month', 'status', 'payment_date',
        'total_gross', 'total_deductions', 'total_net', 'total_paye', 'total_ssnit', 'employee_count',
        'notes', 'created_by', 'finalized_by', 'finalized_at',
    ];

    protected $casts = [
        'period_year'      => 'integer',
        'period_month'     => 'integer',
        'payment_date'     => 'date',
        'total_gross'      => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net'        => 'decimal:2',
        'total_paye'       => 'decimal:2',
        'total_ssnit'      => 'decimal:2',
        'employee_count'   => 'integer',
        'finalized_at'     => 'datetime',
    ];

    const STATUSES = [
        'draft'      => 'Draft',
        'processing' => 'Processing',
        'finalized'  => 'Finalized',
        'paid'       => 'Paid',
    ];

    const MONTHS = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    public function getPeriodLabelAttribute(): string
    {
        return (self::MONTHS[$this->period_month] ?? 'Month') . ' ' . $this->period_year;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PayrollLine::class, 'payroll_run_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}
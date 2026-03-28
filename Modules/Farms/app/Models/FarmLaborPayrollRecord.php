<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmLaborPayrollRecord extends Model
{
    protected $table = 'farm_labor_payroll_records';

    protected $fillable = [
        'company_id',
        'farm_id',
        'farm_worker_id',
        'pay_period_start',
        'pay_period_end',
        'payment_ref',
        'pay_type',
        'days_worked',
        'pieces_count',
        'rate_per_day',
        'rate_per_piece',
        'monthly_salary',
        'gross_pay',
        'deductions_json',
        'net_pay',
        'payment_method',
        'momo_number',
        'status',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end'   => 'date',
        'paid_date'        => 'date',
        'deductions_json'  => 'array',
        'days_worked'      => 'float',
        'pieces_count'     => 'float',
        'gross_pay'        => 'float',
        'net_pay'          => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $record) {
            if (empty($record->payment_ref)) {
                $record->payment_ref = self::generateRef();
            }
        });
    }

    public static function generateRef(): string
    {
        $prefix = 'FLP-' . now()->format('Ym') . '-';
        $last = self::where('payment_ref', 'like', $prefix . '%')
            ->orderByDesc('payment_ref')
            ->value('payment_ref');

        $next = $last ? (int) substr($last, -5) + 1 : 1;

        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function farmWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class);
    }
}
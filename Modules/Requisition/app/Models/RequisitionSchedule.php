<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\CostCentre;
use Modules\HR\Models\Employee;

class RequisitionSchedule extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'requisition_schedules';

    protected $fillable = [
        'company_id',
        'template_id',
        'name',
        'frequency',
        'next_run_at',
        'last_run_at',
        'day_of_week',
        'day_of_month',
        'is_active',
        'requester_employee_id',
        'cost_centre_id',
        'auto_submit',
    ];

    protected function casts(): array
    {
        return [
            'next_run_at' => 'date',
            'last_run_at' => 'date',
            'is_active'   => 'boolean',
            'auto_submit' => 'boolean',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('next_run_at', '<=', now()->toDateString())->where('is_active', true);
    }

    // ── Methods ────────────────────────────────────────────────────────────────

    public function calculateNextRun(): Carbon
    {
        $base = $this->next_run_at instanceof Carbon
            ? $this->next_run_at->copy()
            : Carbon::parse($this->next_run_at);

        return match ($this->frequency) {
            'daily'     => $base->addDay(),
            'weekly'    => $base->addWeek(),
            'biweekly'  => $base->addWeeks(2),
            'monthly'   => $base->addMonth(),
            'quarterly' => $base->addMonths(3),
            default     => $base->addMonth(),
        };
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function template()
    {
        return $this->belongsTo(RequisitionTemplate::class);
    }

    public function requesterEmployee()
    {
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }
}
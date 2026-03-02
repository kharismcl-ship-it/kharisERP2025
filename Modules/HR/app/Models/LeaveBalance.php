<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Events\LeaveAccrued;
use Modules\HR\Events\LeaveBalanceUpdated;
use App\Models\Concerns\BelongsToCompany;

class LeaveBalance extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * The events that should be dispatched.
     */
    protected $dispatchesEvents = [
        'updated' => LeaveBalanceUpdated::class,
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::updated(function ($balance) {
            if ($balance->isDirty(['current_balance', 'used_balance', 'adjustments'])) {
                event(new LeaveBalanceUpdated($balance));
            }
        });
    }

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_leave_balances';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'employee_id',
        'leave_type_id',
        'year',
        'initial_balance',
        'used_balance',
        'current_balance',
        'carried_over',
        'adjustments',
        'last_calculated_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'initial_balance' => 'decimal:2',
            'used_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'carried_over' => 'decimal:2',
            'adjustments' => 'decimal:2',
            'last_calculated_at' => 'datetime',
            'year' => 'integer',
        ];
    }

    /**
     * Get the company that owns the leave balance.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee that owns the leave balance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the leave type that owns the leave balance.
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Calculate the current balance based on initial balance, used balance, and adjustments.
     */
    public function calculateCurrentBalance(): void
    {
        $this->current_balance = $this->initial_balance + $this->carried_over + $this->adjustments - $this->used_balance;
        $this->last_calculated_at = now();
    }

    /**
     * Scope a query to only include balances for a specific year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope a query to only include balances for a specific employee.
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope a query to only include balances for a specific leave type.
     */
    public function scopeForLeaveType($query, $leaveTypeId)
    {
        return $query->where('leave_type_id', $leaveTypeId);
    }

    /**
     * Check if the balance is sufficient for the requested days.
     */
    public function isSufficient(float $requestedDays): bool
    {
        return $this->current_balance >= $requestedDays;
    }

    /**
     * Use leave days from the balance.
     */
    public function useDays(float $days): void
    {
        $this->used_balance += $days;
        $this->calculateCurrentBalance();
    }

    /**
     * Add adjustment to the balance.
     */
    public function addAdjustment(float $days, ?string $note = null): void
    {
        $this->adjustments += $days;
        $this->notes = $this->notes ? $this->notes."\nAdjustment: +{$days} days. {$note}" : "Adjustment: +{$days} days. {$note}";
        $this->calculateCurrentBalance();
    }

    /**
     * Carry over balance to next year.
     */
    public function carryOverToNextYear(float $days, ?string $note = null): void
    {
        $this->carried_over += $days;
        $this->notes = $this->notes ? $this->notes."\nCarried over: +{$days} days to next year. {$note}" : "Carried over: +{$days} days to next year. {$note}";
        $this->calculateCurrentBalance();
    }

    /**
     * Accrue leave days to the balance.
     */
    public function accrueLeave(float $days, ?string $note = null): void
    {
        $this->adjustments += $days;
        $this->notes = $this->notes ? $this->notes."\nAccrued: +{$days} days. {$note}" : "Accrued: +{$days} days. {$note}";
        $this->calculateCurrentBalance();

        event(new LeaveAccrued($this, $days));
    }

    /**
     * Reset balance for new year with optional carryover.
     */
    public function resetForNewYear(float $carryoverLimit = 0, ?string $note = null): void
    {
        $carryoverDays = min($this->current_balance, $carryoverLimit);

        $this->initial_balance = 0;
        $this->used_balance = 0;
        $this->carried_over = $carryoverDays;
        $this->adjustments = 0;
        $this->current_balance = $carryoverDays;
        $this->year = now()->year;

        $this->notes = $this->notes ? $this->notes."\nYear reset: carried over {$carryoverDays} days. {$note}" : "Year reset: carried over {$carryoverDays} days. {$note}";
        $this->last_calculated_at = now();
    }

    /**
     * Get automation configuration for this leave type.
     */
    public function getAutomationConfig(): array
    {
        return [
            'accrual_rate' => $this->leaveType->accrual_rate ?? 0,
            'accrual_frequency' => $this->leaveType->accrual_frequency ?? 'monthly',
            'carryover_limit' => $this->leaveType->carryover_limit ?? 0,
            'max_balance' => $this->leaveType->max_balance ?? null,
        ];
    }

    /**
     * Find or create leave balance for employee and leave type for current year.
     */
    public static function findOrCreateForEmployee(int $employeeId, int $leaveTypeId, ?int $year = null): self
    {
        $year = $year ?? now()->year;

        // Get the employee to find their company_id
        $employee = \Modules\HR\Models\Employee::find($employeeId);

        return static::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'leave_type_id' => $leaveTypeId,
                'year' => $year,
            ],
            [
                'company_id' => $employee->company_id,
                'initial_balance' => 0,
                'used_balance' => 0,
                'current_balance' => 0,
                'carried_over' => 0,
                'adjustments' => 0,
            ]
        );
    }
}

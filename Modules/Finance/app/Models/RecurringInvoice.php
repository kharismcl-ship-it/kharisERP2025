<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringInvoice extends Model
{
    protected $fillable = [
        'company_id',
        'customer_name',
        'customer_type',
        'customer_id',
        'customer_email',
        'description',
        'amount',
        'tax_total',
        'frequency',
        'start_date',
        'end_date',
        'next_run_date',
        'last_run_date',
        'day_of_month',
        'status',
        'invoices_generated',
    ];

    protected $casts = [
        'amount'             => 'decimal:2',
        'tax_total'          => 'decimal:2',
        'start_date'         => 'date',
        'end_date'           => 'date',
        'next_run_date'      => 'date',
        'last_run_date'      => 'date',
        'invoices_generated' => 'integer',
    ];

    public const FREQUENCIES = [
        'daily'     => 'Daily',
        'weekly'    => 'Weekly',
        'monthly'   => 'Monthly',
        'quarterly' => 'Quarterly',
        'annually'  => 'Annually',
    ];

    public const STATUSES = [
        'active'    => 'Active',
        'paused'    => 'Paused',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function generatedInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'entity_id')
            ->where('entity_type', 'recurring_invoice');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDue(): bool
    {
        return $this->isActive()
            && $this->next_run_date <= now()->toDateString()
            && ($this->end_date === null || $this->next_run_date <= $this->end_date);
    }

    /** Advance next_run_date based on frequency */
    public function advanceSchedule(): void
    {
        $next = match ($this->frequency) {
            'daily'     => $this->next_run_date->addDay(),
            'weekly'    => $this->next_run_date->addWeek(),
            'monthly'   => $this->next_run_date->addMonth(),
            'quarterly' => $this->next_run_date->addMonths(3),
            'annually'  => $this->next_run_date->addYear(),
        };

        // Mark completed if we've passed the end date after advancing
        $completed = $this->end_date !== null && $next->gt($this->end_date);

        $this->update([
            'last_run_date'       => now()->toDateString(),
            'next_run_date'       => $next,
            'invoices_generated'  => $this->invoices_generated + 1,
            'status'              => $completed ? 'completed' : $this->status,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDue($query)
    {
        return $query->active()->where('next_run_date', '<=', now()->toDateString());
    }
}

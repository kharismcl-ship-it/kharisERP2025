<?php

namespace Modules\Hostels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\JournalEntry;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_occupant_id',
        'booking_id',
        'hostel_id',
        'amount',
        'deposit_type',
        'status',
        'collected_date',
        'refunded_date',
        'refund_amount',
        'deductions',
        'deduction_reason',
        'invoice_id',
        'journal_entry_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'refund_amount' => 'float',
        'deductions' => 'float',
        'collected_date' => 'date',
        'refunded_date' => 'date',
        'deduction_reason' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_COLLECTED = 'collected';

    const STATUS_REFUNDED = 'refunded';

    const STATUS_PARTIAL_REFUND = 'partial_refund';

    const STATUS_FORFEITED = 'forfeited';

    public function occupant(): BelongsTo
    {
        return $this->belongsTo(HostelOccupant::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCollected($query)
    {
        return $query->where('status', self::STATUS_COLLECTED);
    }

    public function scopeRefundable($query)
    {
        return $query->whereIn('status', [self::STATUS_COLLECTED, self::STATUS_PARTIAL_REFUND]);
    }

    public function calculateRefundAmount(): float
    {
        return max(0, $this->amount - $this->deductions);
    }

    public function markAsCollected($invoiceId = null, $journalEntryId = null)
    {
        $this->update([
            'status' => self::STATUS_COLLECTED,
            'collected_date' => now(),
            'invoice_id' => $invoiceId,
            'journal_entry_id' => $journalEntryId,
        ]);
    }

    public function processRefund($refundAmount, $deductions = 0, $deductionReason = null)
    {
        $status = $refundAmount == $this->amount ? self::STATUS_REFUNDED : self::STATUS_PARTIAL_REFUND;

        $this->update([
            'status' => $status,
            'refund_amount' => $refundAmount,
            'deductions' => $deductions,
            'deduction_reason' => $deductionReason,
            'refunded_date' => now(),
        ]);
    }

    public function markAsForfeited($reason)
    {
        $this->update([
            'status' => self::STATUS_FORFEITED,
            'deductions' => $this->amount,
            'deduction_reason' => $reason,
            'refunded_date' => now(),
        ]);
    }

    public function getRemainingBalance(): float
    {
        return max(0, $this->amount - $this->deductions);
    }

    public function canBeRefunded(): bool
    {
        return in_array($this->status, [self::STATUS_COLLECTED, self::STATUS_PARTIAL_REFUND])
            && $this->getRemainingBalance() > 0;
    }

    public function getDaysHeld(): int
    {
        if (! $this->collected_date) {
            return 0;
        }

        $endDate = $this->refunded_date ?? now();

        return $this->collected_date->diffInDays($endDate);
    }

    public function calculateInterest(float $annualRate = 0): float
    {
        if ($annualRate <= 0) {
            return 0;
        }

        $daysHeld = $this->getDaysHeld();
        $dailyRate = $annualRate / 365;

        return $this->amount * $dailyRate * $daysHeld;
    }

    public function scopeOverdueForRefund($query, int $daysThreshold = 30)
    {
        return $query->where('status', self::STATUS_COLLECTED)
            ->where('collected_date', '<=', now()->subDays($daysThreshold))
            ->whereDoesntHave('booking', function ($query) {
                $query->where('status', 'active');
            });
    }

    protected static function newFactory()
    {
        return \Modules\Hostels\Database\Factories\DepositFactory::new();
    }
}

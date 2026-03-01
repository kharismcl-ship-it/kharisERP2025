<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingPeriod extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'closed_by',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'closed_at'  => 'datetime',
    ];

    public const STATUSES = [
        'open'    => 'Open',
        'closing' => 'Closing (in review)',
        'closed'  => 'Closed',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'closed_by');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'period_id');
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('start_date', '<=', $date)->where('end_date', '>=', $date);
    }

    /** Close the period, locking all journal entries within it */
    public function close(int $userId): void
    {
        $this->update([
            'status'    => 'closed',
            'closed_by' => $userId,
            'closed_at' => now(),
        ]);

        JournalEntry::where('period_id', $this->id)->update(['is_locked' => true]);
    }
}

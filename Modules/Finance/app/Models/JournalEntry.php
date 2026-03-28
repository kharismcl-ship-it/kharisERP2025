<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;

class JournalEntry extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'date',
        'reference',
        'description',
        'period_id',
        'is_locked',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date'      => 'date',
        'is_locked' => 'boolean',
    ];

    /**
     * Get the company that owns this journal entry.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Boot: record audit log when fields are updated.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function (JournalEntry $entry) {
            JournalEntryLog::record(
                $entry->id,
                (int) $entry->company_id,
                'created',
                [],
                "Journal entry {$entry->reference} created."
            );
        });

        static::updating(function (JournalEntry $entry) {
            $tracked = ['date', 'reference', 'description', 'period_id', 'is_locked'];
            foreach ($tracked as $field) {
                if ($entry->isDirty($field)) {
                    JournalEntryLog::record(
                        $entry->id,
                        (int) $entry->company_id,
                        $field === 'is_locked' ? 'period_closed' : 'edited',
                        [
                            'field' => $field,
                            'old'   => $entry->getOriginal($field),
                            'new'   => $entry->getAttribute($field),
                        ]
                    );
                }
            }
        });
    }

    /**
     * Get the journal lines for this entry.
     */
    public function lines()
    {
        return $this->hasMany(JournalLine::class, 'journal_entry_id');
    }

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }

    /**
     * Get the audit logs for this journal entry.
     */
    public function logs()
    {
        return $this->hasMany(JournalEntryLog::class, 'journal_entry_id');
    }
}

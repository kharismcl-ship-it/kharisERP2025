<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class JournalEntryLog extends Model
{
    protected $table = 'fin_journal_entry_logs';

    public $timestamps = false; // only created_at, set manually

    protected $fillable = [
        'journal_entry_id',
        'company_id',
        'user_id',
        'action',
        'field_changed',
        'old_value',
        'new_value',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Record an audit log entry.
     */
    public static function record(
        int    $journalEntryId,
        int    $companyId,
        string $action,
        array  $changes = [],
        string $notes = ''
    ): self {
        $log = new self([
            'journal_entry_id' => $journalEntryId,
            'company_id'       => $companyId,
            'user_id'          => auth()->id() ?? 1,
            'action'           => $action,
            'field_changed'    => $changes['field'] ?? null,
            'old_value'        => isset($changes['old']) ? (string) $changes['old'] : null,
            'new_value'        => isset($changes['new']) ? (string) $changes['new'] : null,
            'ip_address'       => request()->ip(),
            'notes'            => $notes,
        ]);
        $log->created_at = now();
        $log->save();
        return $log;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

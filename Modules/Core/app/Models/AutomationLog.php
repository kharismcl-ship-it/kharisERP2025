<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationLog extends Model
{
    protected $fillable = [
        'automation_setting_id',
        'status',
        'started_at',
        'completed_at',
        'records_processed',
        'error_message',
        'execution_time',
        'details',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'records_processed' => 'integer',
        'execution_time' => 'float',
        'details' => 'array',
    ];

    public function automationSetting(): BelongsTo
    {
        return $this->belongsTo(AutomationSetting::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function markAsStarted()
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted($recordsProcessed = 0, $details = [])
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'records_processed' => $recordsProcessed,
            'execution_time' => $this->started_at->diffInSeconds(now()),
            'details' => $details,
        ]);
    }

    public function markAsFailed($errorMessage, $details = [])
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $errorMessage,
            'execution_time' => $this->started_at->diffInSeconds(now()),
            'details' => $details,
        ]);
    }
}

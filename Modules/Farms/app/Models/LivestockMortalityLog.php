<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class LivestockMortalityLog extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'livestock_batch_id',
        'farm_id',
        'company_id',
        'event_date',
        'count',
        'cause',
        'description',
        'notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'count'      => 'integer',
    ];

    const CAUSES = ['disease', 'injury', 'natural', 'unknown', 'other'];

    protected static function booted(): void
    {
        static::created(function (self $log) {
            $batch = LivestockBatch::find($log->livestock_batch_id);
            if ($batch) {
                $newCount = max(0, $batch->current_count - $log->count);
                $batch->update(['current_count' => $newCount]);
            }
        });
    }

    public function livestockBatch(): BelongsTo
    {
        return $this->belongsTo(LivestockBatch::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
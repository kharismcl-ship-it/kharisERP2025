<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class LivestockEvent extends Model
{
    use BelongsToCompany;

    protected $table = 'livestock_events';

    protected $fillable = [
        'livestock_batch_id', 'farm_id', 'company_id',
        'event_type', 'event_date', 'count',
        'unit_cost', 'total_value',
        'source_or_destination', 'description', 'notes',
    ];

    protected $casts = [
        'event_date'  => 'date',
        'unit_cost'   => 'decimal:4',
        'total_value' => 'decimal:2',
        'count'       => 'integer',
    ];

    const EVENT_TYPES = [
        'birth', 'purchase', 'transfer_in', 'transfer_out', 'sale', 'death', 'other',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $record) {
            if ($record->count && $record->unit_cost && ! $record->isDirty('total_value')) {
                $record->total_value = round($record->count * $record->unit_cost, 2);
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

<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class LivestockFeedRecord extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'livestock_batch_id',
        'farm_id',
        'company_id',
        'feed_date',
        'feed_type',
        'quantity_kg',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'feed_date'   => 'date',
        'quantity_kg' => 'decimal:3',
        'unit_cost'   => 'decimal:4',
        'total_cost'  => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $record) {
            if ($record->quantity_kg && $record->unit_cost && ! $record->isDirty('total_cost')) {
                $record->total_cost = round($record->quantity_kg * $record->unit_cost, 2);
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
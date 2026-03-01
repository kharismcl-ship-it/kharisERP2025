<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class LivestockBatch extends Model
{
    protected $fillable = [
        'farm_id',
        'company_id',
        'batch_reference',
        'animal_type',
        'breed',
        'initial_count',
        'current_count',
        'acquisition_date',
        'acquisition_cost',
        'status',
        'notes',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'initial_count'    => 'integer',
        'current_count'    => 'integer',
    ];

    const ANIMAL_TYPES = ['cattle', 'sheep', 'goats', 'poultry', 'pigs', 'fish', 'other'];
    const STATUSES     = ['active', 'sold', 'slaughtered', 'deceased'];

    protected static function booted(): void
    {
        static::creating(function (self $batch) {
            if (empty($batch->batch_reference)) {
                $batch->batch_reference = 'LB-' . now()->format('Ym') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(LivestockHealthRecord::class);
    }

    public function weightRecords(): HasMany
    {
        return $this->hasMany(LivestockWeightRecord::class);
    }

    public function feedRecords(): HasMany
    {
        return $this->hasMany(LivestockFeedRecord::class);
    }

    public function mortalityLogs(): HasMany
    {
        return $this->hasMany(LivestockMortalityLog::class);
    }

    public function getTotalFeedCostAttribute(): float
    {
        return (float) $this->feedRecords()->sum('total_cost');
    }

    public function getMortalityRateAttribute(): float
    {
        if ($this->initial_count === 0) {
            return 0.0;
        }
        $deceased = $this->initial_count - $this->current_count;
        return round(($deceased / $this->initial_count) * 100, 2);
    }
}

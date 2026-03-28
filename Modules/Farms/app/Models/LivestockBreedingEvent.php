<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class LivestockBreedingEvent extends Model
{
    use BelongsToCompany;

    protected $table = 'livestock_breeding_events';

    protected $fillable = [
        'company_id',
        'livestock_batch_id',
        'event_type',
        'event_date',
        'sire_description',
        'dam_description',
        'method',
        'expected_parturition_date',
        'actual_parturition_date',
        'offspring_count',
        'offspring_alive',
        'conception_rate_pct',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'event_date'               => 'date',
        'expected_parturition_date' => 'date',
        'actual_parturition_date'  => 'date',
        'conception_rate_pct'      => 'float',
        'attachments'              => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $event) {
            if ($event->event_type === 'mating' && ! $event->expected_parturition_date) {
                $batch = $event->batch;
                if ($batch && $batch->gestation_days) {
                    $event->expected_parturition_date = \Carbon\Carbon::parse($event->event_date)
                        ->addDays($batch->gestation_days);
                }
            }
        });
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(LivestockBatch::class, 'livestock_batch_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
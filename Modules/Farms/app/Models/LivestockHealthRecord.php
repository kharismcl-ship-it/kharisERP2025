<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class LivestockHealthRecord extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'livestock_batch_id',
        'farm_id',
        'company_id',
        'event_type',
        'event_date',
        'description',
        'medicine_used',
        'dosage',
        'cost',
        'administered_by',
        'next_due_date',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'event_date'    => 'date',
        'next_due_date' => 'date',
        'cost'          => 'decimal:2',
        'attachments'   => 'array',
    ];

    const EVENT_TYPES = ['treatment', 'vaccination', 'vet_visit', 'deworming', 'other'];

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

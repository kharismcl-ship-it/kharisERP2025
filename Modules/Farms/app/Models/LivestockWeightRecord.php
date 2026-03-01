<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class LivestockWeightRecord extends Model
{
    protected $fillable = [
        'livestock_batch_id',
        'farm_id',
        'company_id',
        'record_date',
        'sample_size',
        'avg_weight_kg',
        'min_weight_kg',
        'max_weight_kg',
        'notes',
    ];

    protected $casts = [
        'record_date'   => 'date',
        'avg_weight_kg' => 'decimal:3',
        'min_weight_kg' => 'decimal:3',
        'max_weight_kg' => 'decimal:3',
        'sample_size'   => 'integer',
    ];

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
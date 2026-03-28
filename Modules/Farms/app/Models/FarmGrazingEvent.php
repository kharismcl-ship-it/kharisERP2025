<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmGrazingEvent extends Model
{
    protected $table = 'farm_grazing_events';

    protected $fillable = [
        'company_id', 'farm_pasture_id', 'livestock_batch_id', 'event_type',
        'event_date', 'foo_kg_ha', 'stock_density', 'days_in_paddock', 'notes',
    ];

    protected $casts = [
        'event_date'    => 'date',
        'foo_kg_ha'     => 'float',
        'stock_density' => 'float',
    ];

    public function pasture(): BelongsTo
    {
        return $this->belongsTo(FarmPasture::class, 'farm_pasture_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(LivestockBatch::class, 'livestock_batch_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
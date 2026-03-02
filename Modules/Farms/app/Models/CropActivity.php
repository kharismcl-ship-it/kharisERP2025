<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class CropActivity extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'crop_cycle_id',
        'farm_id',
        'farm_plot_id',
        'company_id',
        'activity_type',
        'activity_date',
        'description',
        'duration_hours',
        'labour_count',
        'cost',
        'notes',
    ];

    protected $casts = [
        'activity_date'  => 'date',
        'duration_hours' => 'decimal:2',
        'cost'           => 'decimal:2',
        'labour_count'   => 'integer',
    ];

    const ACTIVITY_TYPES = ['planting', 'weeding', 'spraying', 'irrigation', 'pruning', 'harvesting', 'soil_prep', 'other'];

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class, 'farm_plot_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmNdviRecord extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_ndvi_records';

    protected $fillable = [
        'company_id',
        'farm_id',
        'farm_plot_id',
        'recorded_date',
        'ndvi_value',
        'ndvi_min',
        'ndvi_max',
        'source',
        'cloud_cover_pct',
        'stress_detected',
        'alert_sent',
        'zone_data',
        'image_url',
        'notes',
    ];

    protected $casts = [
        'recorded_date'  => 'date',
        'ndvi_value'     => 'float',
        'ndvi_min'       => 'float',
        'ndvi_max'       => 'float',
        'cloud_cover_pct' => 'float',
        'stress_detected' => 'boolean',
        'alert_sent'     => 'boolean',
        'zone_data'      => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function farmPlot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class, 'farm_plot_id');
    }

    public function scopeStressDetected(Builder $query): Builder
    {
        return $query->where('stress_detected', true);
    }

    public function healthLabel(): string
    {
        $v = (float) $this->ndvi_value;

        if ($v < 0.1) return 'Bare/Soil';
        if ($v < 0.3) return 'Sparse';
        if ($v < 0.5) return 'Moderate';
        if ($v < 0.7) return 'Good';

        return 'Excellent';
    }
}
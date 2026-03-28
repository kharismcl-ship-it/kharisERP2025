<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmTrialPlot extends Model
{
    protected $table = 'farm_trial_plots';

    protected $fillable = [
        'farm_trial_id', 'farm_plot_id', 'treatment_label', 'treatment_description',
        'area_ha', 'expected_yield_kg', 'actual_yield_kg', 'total_input_cost',
        'yield_per_ha', 'cost_per_kg', 'notes',
    ];

    protected $casts = [
        'area_ha'           => 'float',
        'expected_yield_kg' => 'float',
        'actual_yield_kg'   => 'float',
    ];

    public function trial(): BelongsTo
    {
        return $this->belongsTo(FarmTrial::class, 'farm_trial_id');
    }

    public function farmPlot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(FarmTrialObservation::class, 'farm_trial_plot_id');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::saving(function (self $model) {
            if ($model->actual_yield_kg && $model->area_ha > 0) {
                $model->yield_per_ha = round($model->actual_yield_kg / $model->area_ha, 4);
            }
            if ($model->total_input_cost && $model->actual_yield_kg > 0) {
                $model->cost_per_kg = round($model->total_input_cost / $model->actual_yield_kg, 4);
            }
        });
    }
}
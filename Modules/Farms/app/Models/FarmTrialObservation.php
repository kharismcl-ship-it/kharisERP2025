<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmTrialObservation extends Model
{
    protected $table = 'farm_trial_observations';

    protected $fillable = [
        'farm_trial_id', 'farm_trial_plot_id', 'observation_date',
        'observation_type', 'value', 'unit', 'notes', 'attachments',
    ];

    protected $casts = [
        'observation_date' => 'date',
        'attachments'      => 'array',
    ];

    public function trial(): BelongsTo
    {
        return $this->belongsTo(FarmTrial::class, 'farm_trial_id');
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(FarmTrialPlot::class, 'farm_trial_plot_id');
    }
}
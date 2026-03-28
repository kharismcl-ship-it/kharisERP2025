<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmTrial extends Model
{
    protected $table = 'farm_trials';

    protected $fillable = [
        'company_id', 'farm_id', 'trial_name', 'trial_type', 'hypothesis', 'objective',
        'start_date', 'end_date', 'crop_season_id', 'crop_name', 'status',
        'methodology', 'conclusion', 'conducted_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function trialPlots(): HasMany
    {
        return $this->hasMany(FarmTrialPlot::class, 'farm_trial_id');
    }

    public function observations(): HasMany
    {
        return $this->hasMany(FarmTrialObservation::class, 'farm_trial_id');
    }

    public function getBestPerformingPlot(): ?FarmTrialPlot
    {
        return $this->trialPlots()->orderByDesc('actual_yield_kg')->first();
    }
}
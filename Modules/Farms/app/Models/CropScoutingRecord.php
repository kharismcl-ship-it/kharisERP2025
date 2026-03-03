<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class CropScoutingRecord extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'crop_cycle_id',
        'farm_id',
        'farm_plot_id',
        'company_id',
        'scouting_date',
        'scouted_by',
        'finding_type',
        'severity',
        'description',
        'recommended_action',
        'follow_up_date',
        'resolved_at',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'scouting_date'  => 'date',
        'follow_up_date' => 'date',
        'resolved_at'    => 'datetime',
        'attachments'    => 'array',
    ];

    const FINDING_TYPES = ['pest', 'disease', 'weed', 'nutrient_deficiency', 'weather_damage', 'normal', 'other'];
    const SEVERITIES    = ['low', 'medium', 'high', 'critical'];

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

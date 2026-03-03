<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

class FarmMilestone extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_milestones';

    protected $fillable = [
        'farm_season_id',
        'farm_id',
        'company_id',
        'title',
        'description',
        'milestone_type',
        'target_date',
        'actual_date',
        'status',
        'progress_notes',
        'attachments',
    ];

    protected $casts = [
        'target_date' => 'date',
        'actual_date' => 'date',
        'attachments' => 'array',
    ];

    const MILESTONE_TYPES = ['land_prep', 'planting', 'growing', 'scouting', 'harvesting', 'selling', 'reporting', 'other'];
    const STATUSES        = ['pending', 'in_progress', 'completed', 'missed'];

    public function farmSeason(): BelongsTo
    {
        return $this->belongsTo(FarmSeason::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}

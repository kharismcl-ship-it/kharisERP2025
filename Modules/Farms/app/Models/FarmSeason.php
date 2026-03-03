<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class FarmSeason extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_seasons';

    protected $fillable = [
        'farm_id',
        'company_id',
        'name',
        'description',
        'season_year',
        'start_date',
        'end_date',
        'status',
        'target_yield',
        'yield_unit',
        'total_budget',
        'actual_cost',
        'notes',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'target_yield' => 'decimal:3',
        'total_budget' => 'decimal:2',
        'actual_cost'  => 'decimal:2',
    ];

    const STATUSES = ['planning', 'active', 'completed', 'cancelled'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(FarmMilestone::class);
    }
}

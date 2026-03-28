<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmAgronomistVisit extends Model
{
    protected $table = 'farm_agronomist_visits';

    protected $fillable = [
        'company_id',
        'farm_agronomist_id',
        'farm_id',
        'crop_cycle_id',
        'visit_date',
        'visit_type',
        'observations',
        'recommendations',
        'follow_up_required',
        'follow_up_date',
        'attachments',
        'status',
    ];

    protected $casts = [
        'visit_date'         => 'date',
        'follow_up_date'     => 'date',
        'follow_up_required' => 'boolean',
        'attachments'        => 'array',
    ];

    public function agronomist(): BelongsTo
    {
        return $this->belongsTo(FarmAgronomist::class, 'farm_agronomist_id');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }
}
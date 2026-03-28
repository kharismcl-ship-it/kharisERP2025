<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmInsurancePolicy extends Model
{
    protected $table = 'farm_insurance_policies';

    protected $fillable = [
        'company_id',
        'farm_id',
        'policy_number',
        'insurer_name',
        'insurance_type',
        'crop_cycle_id',
        'livestock_batch_id',
        'covered_crop',
        'covered_area_ha',
        'sum_insured',
        'premium_amount',
        'premium_paid_date',
        'start_date',
        'end_date',
        'trigger_description',
        'status',
        'claim_amount',
        'claim_date',
        'claim_status',
        'claim_notes',
        'document_path',
    ];

    protected $casts = [
        'start_date'         => 'date',
        'end_date'           => 'date',
        'premium_paid_date'  => 'date',
        'claim_date'         => 'date',
        'sum_insured'        => 'float',
        'premium_amount'     => 'float',
        'claim_amount'       => 'float',
        'covered_area_ha'    => 'float',
    ];

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

    public function livestockBatch(): BelongsTo
    {
        return $this->belongsTo(LivestockBatch::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isExpired(): bool
    {
        return $this->end_date !== null && $this->end_date->isPast();
    }

    public function hasClaim(): bool
    {
        return ! empty($this->claim_status);
    }
}

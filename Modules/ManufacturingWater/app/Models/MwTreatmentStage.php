<?php

namespace Modules\ManufacturingWater\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MwTreatmentStage extends Model
{
    protected $table = 'mw_treatment_stages';

    protected $fillable = [
        'plant_id',
        'name',
        'stage_order',
        'stage_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'stage_order' => 'integer',
    ];

    const STAGE_TYPES = ['filtration', 'chlorination', 'UV', 'RO', 'ozone', 'sedimentation', 'fluoridation', 'softening'];

    public function plant(): BelongsTo
    {
        return $this->belongsTo(MwPlant::class, 'plant_id');
    }

    public function chemicalUsages(): HasMany
    {
        return $this->hasMany(MwChemicalUsage::class, 'treatment_stage_id');
    }
}
<?php

namespace Modules\ManufacturingPaper\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpQualityRecord extends Model
{
    protected $table = 'mp_quality_records';

    protected $fillable = [
        'production_batch_id',
        'company_id',
        'test_date',
        'tested_by',
        'tensile_cd',
        'tensile_md',
        'burst_strength',
        'moisture_percent',
        'brightness',
        'opacity',
        'roughness',
        'basis_weight',
        'passed',
        'notes',
    ];

    protected $casts = [
        'test_date'       => 'date',
        'passed'          => 'boolean',
        'tensile_cd'      => 'decimal:3',
        'tensile_md'      => 'decimal:3',
        'burst_strength'  => 'decimal:3',
        'moisture_percent'=> 'decimal:2',
        'brightness'      => 'decimal:2',
        'opacity'         => 'decimal:2',
        'roughness'       => 'decimal:2',
        'basis_weight'    => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(MpProductionBatch::class, 'production_batch_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
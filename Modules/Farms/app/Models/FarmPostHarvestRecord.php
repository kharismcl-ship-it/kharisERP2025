<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmPostHarvestRecord extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_post_harvest_records';

    protected $fillable = [
        'company_id',
        'farm_id',
        'harvest_record_id',
        'farm_storage_location_id',
        'farm_produce_lot_id',
        'record_type',
        'grade_a_qty',
        'grade_b_qty',
        'grade_c_qty',
        'reject_qty',
        'total_loss_qty',
        'loss_cause',
        'treatment_type',
        'quality_test_type',
        'quality_test_result',
        'quality_test_passed',
        'recorded_by_worker_id',
        'notes',
        'record_date',
    ];

    protected $casts = [
        'record_date'         => 'date',
        'grade_a_qty'         => 'float',
        'grade_b_qty'         => 'float',
        'grade_c_qty'         => 'float',
        'reject_qty'          => 'float',
        'total_loss_qty'      => 'float',
        'quality_test_passed' => 'boolean',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function harvestRecord(): BelongsTo
    {
        return $this->belongsTo(HarvestRecord::class);
    }

    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(FarmStorageLocation::class, 'farm_storage_location_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(FarmProduceLot::class, 'farm_produce_lot_id');
    }

    public function recordedByWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class, 'recorded_by_worker_id');
    }
}
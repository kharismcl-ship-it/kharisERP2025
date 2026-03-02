<?php

namespace Modules\ManufacturingWater\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ManufacturingWater\Events\MwWaterTestFailed;
use App\Models\Concerns\BelongsToCompany;

class MwWaterTestRecord extends Model
{
    use BelongsToCompany;

    protected $table = 'mw_water_test_records';

    protected $fillable = [
        'plant_id',
        'company_id',
        'test_date',
        'test_type',
        'ph',
        'turbidity_ntu',
        'tds_ppm',
        'coliform_count',
        'chlorine_residual',
        'temperature_c',
        'dissolved_oxygen',
        'passed',
        'tested_by',
        'notes',
    ];

    protected $casts = [
        'test_date'        => 'date',
        'passed'           => 'boolean',
        'ph'               => 'decimal:2',
        'turbidity_ntu'    => 'decimal:3',
        'tds_ppm'          => 'decimal:2',
        'coliform_count'   => 'decimal:2',
        'chlorine_residual'=> 'decimal:3',
        'temperature_c'    => 'decimal:2',
        'dissolved_oxygen' => 'decimal:3',
    ];

    const TEST_TYPES = ['raw', 'treated', 'final', 'distribution'];

    protected static function booted(): void
    {
        static::created(function (self $record) {
            if ($record->passed === false) {
                MwWaterTestFailed::dispatch($record);
            }
        });
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(MwPlant::class, 'plant_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

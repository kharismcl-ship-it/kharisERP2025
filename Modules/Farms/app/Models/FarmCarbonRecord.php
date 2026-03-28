<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmCarbonRecord extends Model
{
    protected $table = 'farm_carbon_records';

    protected $fillable = [
        'company_id', 'farm_id', 'crop_cycle_id', 'livestock_batch_id', 'record_period',
        'period_start', 'period_end', 'fertilizer_emissions_tco2e', 'fuel_emissions_tco2e',
        'livestock_emissions_tco2e', 'electricity_emissions_tco2e', 'other_emissions_tco2e',
        'total_emissions_tco2e', 'soil_sequestration_tco2e', 'tree_sequestration_tco2e',
        'net_emissions_tco2e', 'farm_area_ha', 'total_production_kg', 'emissions_per_ha',
        'emissions_per_kg', 'water_used_m3', 'water_per_tonne_produce', 'methodology_notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::saving(function (self $model) {
            $model->total_emissions_tco2e = array_sum([
                (float) $model->fertilizer_emissions_tco2e,
                (float) $model->fuel_emissions_tco2e,
                (float) $model->livestock_emissions_tco2e,
                (float) $model->electricity_emissions_tco2e,
                (float) $model->other_emissions_tco2e,
            ]);
            $model->net_emissions_tco2e = $model->total_emissions_tco2e
                - (float) $model->soil_sequestration_tco2e
                - (float) $model->tree_sequestration_tco2e;
            if ($model->farm_area_ha > 0) {
                $model->emissions_per_ha = round($model->net_emissions_tco2e / $model->farm_area_ha, 4);
            }
            if ($model->total_production_kg > 0) {
                $model->emissions_per_kg = round($model->net_emissions_tco2e / $model->total_production_kg, 6);
                $model->water_per_tonne_produce = round(((float) $model->water_used_m3 * 1000) / $model->total_production_kg, 4);
            }
        });
    }
}
<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmEquipmentLog extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_equipment_logs';

    protected $fillable = [
        'company_id',
        'farm_equipment_id',
        'farm_id',
        'farm_plot_id',
        'operator_worker_id',
        'operation_type',
        'started_at',
        'ended_at',
        'hours_used',
        'area_covered_ha',
        'fuel_used_litres',
        'fuel_cost',
        'cost_per_ha',
        'notes',
    ];

    protected $casts = [
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
        'hours_used'       => 'float',
        'area_covered_ha'  => 'float',
        'fuel_used_litres' => 'float',
        'fuel_cost'        => 'float',
        'cost_per_ha'      => 'float',
    ];

    const OPERATION_TYPES = [
        'Ploughing', 'Spraying', 'Planting', 'Harvesting', 'Transport', 'Other',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $log) {
            if ($log->fuel_cost && $log->area_covered_ha && $log->area_covered_ha > 0) {
                $log->cost_per_ha = round($log->fuel_cost / $log->area_covered_ha, 4);
            }
        });
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(FarmEquipment::class, 'farm_equipment_id');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function farmPlot(): BelongsTo
    {
        return $this->belongsTo(FarmPlot::class, 'farm_plot_id');
    }

    public function operatorWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class, 'operator_worker_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
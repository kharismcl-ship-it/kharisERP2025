<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;

class FarmEquipmentMaintenanceSchedule extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_equipment_maintenance_schedules';

    protected $fillable = [
        'company_id',
        'farm_equipment_id',
        'service_type',
        'interval_type',
        'interval_value',
        'last_service_at',
        'last_service_hours',
        'next_service_date',
        'next_service_hours',
        'notes',
    ];

    protected $casts = [
        'last_service_at'    => 'date',
        'next_service_date'  => 'date',
        'last_service_hours' => 'float',
        'next_service_hours' => 'float',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(FarmEquipment::class, 'farm_equipment_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
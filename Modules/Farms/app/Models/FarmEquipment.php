<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;

class FarmEquipment extends Model
{
    protected $table = 'farm_equipment';

    protected $fillable = [
        'farm_id', 'company_id',
        'name', 'equipment_type',
        'make', 'model', 'year', 'serial_number',
        'purchase_date', 'purchase_price', 'current_value',
        'status', 'last_service_date', 'next_service_date', 'notes',
    ];

    protected $casts = [
        'purchase_date'     => 'date',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'purchase_price'    => 'decimal:2',
        'current_value'     => 'decimal:2',
    ];

    const EQUIPMENT_TYPES = [
        'tractor', 'irrigation', 'seeder', 'harvester', 'sprayer', 'vehicle', 'other',
    ];

    const STATUSES = ['active', 'maintenance', 'retired'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(FarmTask::class, 'farm_equipment_id');
    }
}
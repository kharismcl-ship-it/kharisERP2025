<?php

namespace Modules\Fleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class MaintenanceRecord extends Model
{
    protected $fillable = [
        'vehicle_id',
        'company_id',
        'type',
        'description',
        'service_date',
        'mileage_at_service',
        'next_service_date',
        'next_service_mileage',
        'service_provider',
        'cost',
        'status',
        'notes',
        'finance_expense_id',
        'purchase_order_id',
    ];

    protected $casts = [
        'service_date'          => 'date',
        'next_service_date'     => 'date',
        'mileage_at_service'    => 'decimal:2',
        'next_service_mileage'  => 'decimal:2',
        'cost'                  => 'decimal:2',
    ];

    const TYPES = ['routine', 'repair', 'inspection', 'tire_change', 'oil_change', 'battery', 'other'];
    const STATUSES = ['scheduled', 'in_progress', 'completed'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

<?php

namespace Modules\Fleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use Modules\Fleet\Events\MaintenanceCompleted;
use App\Models\Concerns\BelongsToCompany;

class MaintenanceRecord extends Model
{
    use BelongsToCompany;

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
        'item_id',
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

    protected static function booted(): void
    {
        static::updated(function (self $record) {
            if ($record->wasChanged('status') && $record->status === 'completed') {
                MaintenanceCompleted::dispatch($record);
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(\Modules\ProcurementInventory\Models\Item::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

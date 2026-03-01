<?php

namespace Modules\Fleet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use App\Models\User;
use Modules\HR\Models\Employee;

class DriverAssignment extends Model
{
    protected $fillable = [
        'vehicle_id',
        'company_id',
        'employee_id',
        'user_id',
        'assigned_from',
        'assigned_until',
        'is_primary',
        'notes',
    ];

    protected $casts = [
        'assigned_from'  => 'date',
        'assigned_until' => 'date',
        'is_primary'     => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->assigned_until === null || $this->assigned_until->isFuture();
    }
}

<?php

namespace Modules\Construction\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\HR\Models\Employee;

class SiteMonitor extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'construction_project_id',
        'employee_id',
        'name',
        'email',
        'phone',
        'monitor_type',
        'role',
        'is_active',
        'appointed_date',
        'notes',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'appointed_date' => 'date',
    ];

    const MONITOR_TYPES = ['internal', 'external', 'consultant'];
    const ROLES         = ['site_engineer', 'quality_inspector', 'safety_officer', 'independent_monitor', 'other'];

    protected static function booted(): void
    {
        static::creating(function (self $monitor) {
            if ($monitor->employee_id && !$monitor->name) {
                $employee = Employee::find($monitor->employee_id);
                if ($employee) {
                    $monitor->name  = $monitor->name  ?: $employee->full_name;
                    $monitor->email = $monitor->email ?: $employee->email;
                    $monitor->phone = $monitor->phone ?: $employee->phone;
                }
            }
        });

        static::updating(function (self $monitor) {
            if ($monitor->isDirty('employee_id') && $monitor->employee_id) {
                $employee = Employee::find($monitor->employee_id);
                if ($employee) {
                    $monitor->name  = $employee->full_name;
                    $monitor->email = $employee->email;
                    $monitor->phone = $employee->phone;
                }
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(MonitoringReport::class);
    }
}

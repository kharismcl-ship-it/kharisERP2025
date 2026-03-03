<?php

namespace Modules\Construction\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\HR\Models\Employee;

class ConstructionWorker extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'construction_project_id',
        'employee_id',
        'contractor_id',
        'name',
        'phone',
        'email',
        'national_id',
        'category',
        'trade',
        'daily_rate',
        'contract_start',
        'contract_end',
        'status',
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end'   => 'date',
        'daily_rate'     => 'decimal:2',
    ];

    const CATEGORIES = ['day_labour', 'project_staff', 'subcontractor'];
    const STATUSES   = ['active', 'inactive', 'suspended'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(WorkerAttendance::class);
    }

    public function taskAssignments(): HasMany
    {
        return $this->hasMany(WorkerTaskAssignment::class);
    }
}

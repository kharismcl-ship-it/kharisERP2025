<?php

namespace Modules\ITSupport\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class ItActivity extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'it_activities';

    protected $fillable = [
        'company_id',
        'performed_by_employee_id',
        'activity_type',
        'title',
        'description',
        'scheduled_at',
        'completed_at',
        'status',
        'affected_systems',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at'  => 'datetime',
            'completed_at'  => 'datetime',
        ];
    }

    const ACTIVITY_TYPES = [
        'maintenance'    => 'Maintenance',
        'audit'          => 'Audit',
        'deployment'     => 'Deployment',
        'configuration'  => 'Configuration',
        'backup'         => 'Backup',
        'security_check' => 'Security Check',
        'upgrade'        => 'Upgrade',
        'other'          => 'Other',
    ];

    const STATUSES = [
        'planned'     => 'Planned',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function performedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'performed_by_employee_id');
    }
}

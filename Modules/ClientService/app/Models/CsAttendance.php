<?php

namespace Modules\ClientService\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;

class CsAttendance extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'client_service_attendances';

    protected $fillable = [
        'company_id',
        'employee_id',
        'department_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'status',
        'late_minutes',
        'overtime_minutes',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    const STATUSES = [
        'present'  => 'Present',
        'absent'   => 'Absent',
        'late'     => 'Late',
        'half_day' => 'Half Day',
        'leave'    => 'Leave',
        'holiday'  => 'Holiday',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

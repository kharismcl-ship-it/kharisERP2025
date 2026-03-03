<?php

namespace Modules\ITSupport\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\ITSupport\Events\ItTrainingInviteSent;

class ItTrainingSession extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'it_training_sessions';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'trainer_employee_id',
        'department_id',
        'session_type',
        'scheduled_at',
        'duration_minutes',
        'location',
        'max_attendees',
        'status',
        'materials_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    const SESSION_TYPES = [
        'workshop'    => 'Workshop',
        'webinar'     => 'Webinar',
        'self_paced'  => 'Self-Paced',
        'on_the_job'  => 'On The Job',
        'certification' => 'Certification',
    ];

    const STATUSES = [
        'planned'   => 'Planned',
        'ongoing'   => 'Ongoing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (ItTrainingSession $session) {
            event(new ItTrainingInviteSent($session));
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function trainerEmployee()
    {
        return $this->belongsTo(Employee::class, 'trainer_employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendees()
    {
        return $this->hasMany(ItTrainingAttendee::class, 'it_training_session_id');
    }
}

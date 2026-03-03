<?php

namespace Modules\ClientService\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ClientService\Events\VisitorCheckedOut;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;

class CsVisitor extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'client_service_visitors';

    protected $fillable = [
        'company_id',
        'full_name',
        'phone',
        'email',
        'id_type',
        'id_number',
        'organization',
        'purpose_of_visit',
        'host_employee_id',
        'department_id',
        'check_in_at',
        'check_out_at',
        'badge_number',
        'items_brought',
        'photo_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at'  => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    const ID_TYPES = [
        'national_id'      => 'National ID',
        'passport'         => 'Passport',
        'drivers_license'  => 'Driver\'s License',
        'other'            => 'Other',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function (CsVisitor $visitor) {
            if ($visitor->isDirty('check_out_at') && $visitor->check_out_at !== null && $visitor->getOriginal('check_out_at') === null) {
                event(new VisitorCheckedOut($visitor));
            }
        });
    }

    public function getIsCheckedOutAttribute(): bool
    {
        return $this->check_out_at !== null;
    }

    public function getDurationAttribute(): ?string
    {
        if (! $this->check_out_at) {
            return null;
        }

        $minutes = (int) $this->check_in_at->diffInMinutes($this->check_out_at);
        $hours   = intdiv($minutes, 60);
        $mins    = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }

        return "{$mins}m";
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function hostEmployee()
    {
        return $this->belongsTo(Employee::class, 'host_employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

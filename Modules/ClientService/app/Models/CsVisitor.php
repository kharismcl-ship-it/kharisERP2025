<?php

namespace Modules\ClientService\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\ClientService\Events\VisitorCheckedOut;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;

class CsVisitor extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'client_service_visitors';

    protected $fillable = [
        'company_id',
        'visitor_profile_id',
        'group_lead_visitor_id',
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
        'check_in_token',
        'items_brought',
        'photo_path',
        'check_in_signature',
        'notes',
        'communication_opt_in',
        'checked_in_by_user_id',
        'checked_out_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at'          => 'datetime',
            'check_out_at'         => 'datetime',
            'communication_opt_in' => 'boolean',
        ];
    }

    const ID_TYPES = [
        'national_id'     => 'National ID',
        'passport'        => 'Passport',
        'drivers_license' => "Driver's License",
        'other'           => 'Other',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (CsVisitor $visitor) {
            if (empty($visitor->check_in_token)) {
                $visitor->check_in_token = (string) Str::uuid();
            }
        });

        static::updating(function (CsVisitor $visitor) {
            if (
                $visitor->isDirty('check_out_at') &&
                $visitor->check_out_at !== null &&
                $visitor->getOriginal('check_out_at') === null
            ) {
                event(new VisitorCheckedOut($visitor));
            }
        });
    }

    // ── Computed attributes ────────────────────────────────────────

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

        return $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
    }

    // ── Relationships ──────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function visitorProfile()
    {
        return $this->belongsTo(CsVisitorProfile::class, 'visitor_profile_id');
    }

    /** The lead visitor when this record is a group member. */
    public function leadVisitor()
    {
        return $this->belongsTo(CsVisitor::class, 'group_lead_visitor_id');
    }

    /** Group members when this record is the group lead. */
    public function groupMembers()
    {
        return $this->hasMany(CsVisitor::class, 'group_lead_visitor_id');
    }

    public function badge()
    {
        return $this->hasOne(CsVisitorBadge::class, 'issued_to_visitor_id');
    }

    public function hostEmployee()
    {
        return $this->belongsTo(Employee::class, 'host_employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by_user_id');
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by_user_id');
    }
}

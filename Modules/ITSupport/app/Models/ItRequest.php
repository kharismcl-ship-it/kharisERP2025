<?php

namespace Modules\ITSupport\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KirschbaumDevelopment\Commentions\Traits\InteractsWithComments;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\ITSupport\Events\ItRequestStatusChanged;

class ItRequest extends Model
{
    use HasFactory, BelongsToCompany, SoftDeletes, InteractsWithComments;

    protected $table = 'it_requests';

    protected $fillable = [
        'company_id',
        'reference',
        'requester_employee_id',
        'department_id',
        'category',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to_employee_id',
        'estimated_resolution_date',
        'resolved_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'estimated_resolution_date' => 'date',
            'resolved_at'               => 'datetime',
        ];
    }

    const CATEGORIES = [
        'hardware'  => 'Hardware',
        'software'  => 'Software',
        'network'   => 'Network',
        'access'    => 'Access',
        'email'     => 'Email',
        'training'  => 'Training',
        'other'     => 'Other',
    ];

    const PRIORITIES = [
        'low'      => 'Low',
        'medium'   => 'Medium',
        'high'     => 'High',
        'critical' => 'Critical',
    ];

    const STATUSES = [
        'open'         => 'Open',
        'in_progress'  => 'In Progress',
        'pending_info' => 'Pending Info',
        'resolved'     => 'Resolved',
        'closed'       => 'Closed',
        'cancelled'    => 'Cancelled',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ItRequest $request) {
            if (empty($request->reference)) {
                $prefix = 'ITR-' . now()->format('Ym') . '-';
                $last   = static::withTrashed()
                    ->where('reference', 'like', $prefix . '%')
                    ->orderByDesc('id')
                    ->first();
                $seq              = $last ? ((int) substr($last->reference, -5)) + 1 : 1;
                $request->reference = $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function (ItRequest $request) {
            if ($request->isDirty('status')) {
                $oldStatus = $request->getOriginal('status');
                event(new ItRequestStatusChanged($request, $oldStatus));
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requesterEmployee()
    {
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }

    public function assignedToEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to_employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

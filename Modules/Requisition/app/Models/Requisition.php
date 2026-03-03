<?php

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\Commentions\HasComments;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\Requisition\Events\RequisitionStatusChanged;
use Modules\Requisition\Events\RequisitionShared;

class Requisition extends Model
{
    use HasFactory, BelongsToCompany, SoftDeletes, HasComments;

    protected $fillable = [
        'company_id',
        'requester_employee_id',
        'target_company_id',
        'target_department_id',
        'reference',
        'request_type',
        'title',
        'description',
        'urgency',
        'status',
        'cost_centre_id',
        'total_estimated_cost',
        'approved_by',
        'approved_at',
        'fulfilled_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_estimated_cost' => 'decimal:2',
            'approved_at'          => 'datetime',
            'fulfilled_at'         => 'datetime',
        ];
    }

    const TYPES = [
        'material'  => 'Material',
        'fund'      => 'Fund',
        'general'   => 'General',
        'equipment' => 'Equipment',
        'service'   => 'Service',
        'other'     => 'Other',
    ];

    const STATUSES = [
        'draft'        => 'Draft',
        'submitted'    => 'Submitted',
        'under_review' => 'Under Review',
        'approved'     => 'Approved',
        'rejected'     => 'Rejected',
        'fulfilled'    => 'Fulfilled',
    ];

    const URGENCIES = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Requisition $requisition) {
            if (empty($requisition->reference)) {
                $prefix  = 'REQ-' . now()->format('Ym') . '-';
                $last    = static::withTrashed()
                    ->where('reference', 'like', $prefix . '%')
                    ->orderByDesc('id')
                    ->first();
                $seq     = $last ? ((int) substr($last->reference, -5)) + 1 : 1;
                $requisition->reference = $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function (Requisition $requisition) {
            if ($requisition->isDirty('status')) {
                $oldStatus = $requisition->getOriginal('status');
                event(new RequisitionStatusChanged($requisition, $oldStatus));
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function targetCompany()
    {
        return $this->belongsTo(Company::class, 'target_company_id');
    }

    public function requesterEmployee()
    {
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }

    public function targetDepartment()
    {
        return $this->belongsTo(Department::class, 'target_department_id');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(RequisitionItem::class);
    }

    public function approvers()
    {
        return $this->hasMany(RequisitionApprover::class);
    }
}

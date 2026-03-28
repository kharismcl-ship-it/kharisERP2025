<?php

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\Commentions\HasComments;
use Modules\Finance\Models\CostCentre;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\ProcurementInventory\Models\Vendor;
use Modules\Requisition\Events\RequisitionStatusChanged;

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
        'due_by',
        'notification_channels',
        'template_id',
        'preferred_vendor_id',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_estimated_cost'  => 'decimal:2',
            'approved_at'           => 'datetime',
            'fulfilled_at'          => 'datetime',
            'due_by'                => 'date',
            'notification_channels' => 'array',
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
        'draft'            => 'Draft',
        'submitted'        => 'Submitted',
        'under_review'     => 'Under Review',
        'pending_revision' => 'Pending Revision',
        'approved'         => 'Approved',
        'rejected'         => 'Rejected',
        'fulfilled'        => 'Fulfilled',
        'closed'           => 'Closed',
        'cancelled'        => 'Cancelled',
    ];

    const URGENCIES = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    const NOTIFICATION_CHANNELS = [
        'email'    => 'Email',
        'sms'      => 'SMS',
        'whatsapp' => 'WhatsApp',
        'database' => 'In-App',
    ];

    /** Types that support auto cost calculation from items */
    const COSTED_TYPES = ['material', 'equipment'];

    /** Statuses considered terminal / archived */
    const RESOLVED_STATUSES = ['approved', 'fulfilled', 'closed', 'rejected', 'cancelled'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Requisition $requisition) {
            if (empty($requisition->reference)) {
                $prefix = 'REQ-' . now()->format('Ym') . '-';
                $last   = static::withTrashed()
                    ->where('reference', 'like', $prefix . '%')
                    ->orderByDesc('id')
                    ->first();
                $seq    = $last ? ((int) substr($last->reference, -5)) + 1 : 1;
                $requisition->reference = $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
            }

            if (empty($requisition->notification_channels)) {
                $requisition->notification_channels = ['email', 'database'];
            }
        });

        static::created(function (Requisition $requisition) {
            RequisitionActivity::log($requisition, 'requisition_created', "Request \"{$requisition->title}\" created.");
        });

        static::updating(function (Requisition $requisition) {
            if ($requisition->isDirty('status')) {
                $oldStatus = $requisition->getOriginal('status');
                event(new RequisitionStatusChanged($requisition, $oldStatus));
                RequisitionActivity::log(
                    $requisition,
                    'status_changed',
                    'Status changed from ' . (self::STATUSES[$oldStatus] ?? $oldStatus) . ' to ' . (self::STATUSES[$requisition->status] ?? $requisition->status) . '.',
                    [],
                    $oldStatus,
                    $requisition->status
                );
            }
        });
    }

    /**
     * Recalculate total_estimated_cost from items that have unit_cost set.
     */
    public function recalculateTotalFromItems(): void
    {
        $itemsSum = $this->items()->whereNotNull('unit_cost')->sum('total_cost');

        if ($itemsSum > 0) {
            $this->withoutEvents(fn () => $this->update(['total_estimated_cost' => $itemsSum]));
        }
    }

    /**
     * Return how much this requisition exceeds the cost centre budget (0 = within budget).
     */
    public function budgetOverage(): float
    {
        if (! $this->cost_centre_id || ! $this->total_estimated_cost) {
            return 0;
        }

        $budgetAmount = $this->costCentre?->budget_amount;
        if (! $budgetAmount) {
            return 0;
        }

        $committed = static::where('cost_centre_id', $this->cost_centre_id)
            ->whereNotIn('status', ['rejected', 'closed'])
            ->where('id', '!=', $this->id)
            ->sum('total_estimated_cost');

        return max(0, (float) ($this->total_estimated_cost - ($budgetAmount - $committed)));
    }

    public function getNotificationChannels(): array
    {
        return $this->notification_channels ?? ['email', 'database'];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

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

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function template()
    {
        return $this->belongsTo(RequisitionTemplate::class);
    }

    public function preferredVendor()
    {
        return $this->belongsTo(Vendor::class, 'preferred_vendor_id');
    }

    public function items()
    {
        return $this->hasMany(RequisitionItem::class);
    }

    public function approvers()
    {
        return $this->hasMany(RequisitionApprover::class)->orderBy('order');
    }

    public function parties()
    {
        return $this->hasMany(RequisitionParty::class);
    }

    public function attachments()
    {
        return $this->hasMany(RequisitionAttachment::class);
    }

    public function activities()
    {
        return $this->hasMany(RequisitionActivity::class)->orderByDesc('created_at');
    }

    public function customFieldValues()
    {
        return $this->hasMany(RequisitionCustomFieldValue::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->due_by && $this->due_by->isPast()
            && ! in_array($this->status, self::RESOLVED_STATUSES);
    }

    public function isResolved(): bool
    {
        return in_array($this->status, self::RESOLVED_STATUSES);
    }
}
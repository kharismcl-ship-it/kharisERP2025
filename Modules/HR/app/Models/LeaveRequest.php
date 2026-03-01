<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\HR\Services\LeaveApprovalService;
use Modules\HR\Services\LeaveNotificationService;

class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'hr_leave_requests';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'status',
        'reason',
        'approved_by_employee_id',
        'approved_at',
        'rejected_reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the company that owns the leave request.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee who requested the leave.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the leave type for this request.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Get the employee who approved the leave request.
     */
    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by_employee_id');
    }

    /**
     * Get the attachments for the leave request.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(LeaveAttachment::class);
    }

    /**
     * Get the public attachments for the leave request.
     */
    public function publicAttachments(): HasMany
    {
        return $this->attachments()->public();
    }

    /**
     * Get the private attachments for the leave request.
     */
    public function privateAttachments(): HasMany
    {
        return $this->attachments()->private();
    }

    /**
     * Get the approval requests for this leave request.
     */
    public function approvalRequests(): HasMany
    {
        return $this->hasMany(LeaveApprovalRequest::class);
    }

    /**
     * Get the current pending approval request.
     */
    public function currentApprovalRequest(): HasOne
    {
        return $this->hasOne(LeaveApprovalRequest::class)->where('status', 'pending')->latest();
    }

    /**
     * Check if leave request has multi-level approval workflow.
     */
    public function hasMultiLevelApproval(): bool
    {
        return $this->approvalRequests()->exists();
    }

    /**
     * Get the next approval level for this leave request.
     */
    public function getNextApprovalLevel(): ?LeaveApprovalLevel
    {
        $lastApproval = $this->approvalRequests()->orderByDesc('id')->first();

        if (! $lastApproval) {
            // First approval
            $workflow = app(LeaveApprovalService::class)->getWorkflowForLeaveRequest($this);

            return $workflow?->activeLevels()->first();
        }

        if ($lastApproval->isRejected()) {
            return null;
        }

        if ($lastApproval->isApproved()) {
            $workflow = app(LeaveApprovalService::class)->getWorkflowForLeaveRequest($this);
            $nextLevel = $workflow?->activeLevels()
                ->where('approval_order', '>', $lastApproval->approvalLevel->approval_order)
                ->first();

            return $nextLevel;
        }

        return null;
    }

    /**
     * Check if leave request requires additional approvals.
     */
    public function requiresAdditionalApprovals(): bool
    {
        return $this->getNextApprovalLevel() !== null;
    }

    /**
     * Get the current approver for this leave request.
     */
    public function getCurrentApprover(): ?Employee
    {
        $nextLevel = $this->getNextApprovalLevel();
        if (! $nextLevel) {
            return null;
        }

        return $nextLevel->getApproverForEmployee($this->employee);
    }

    /**
     * Calculate the number of leave days requested.
     */
    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (LeaveRequest $leaveRequest) {
            if ($leaveRequest->status === 'pending') {
                app(LeaveNotificationService::class)->notifyLeaveRequestSubmitted($leaveRequest);
            }
        });

        static::updated(function (LeaveRequest $leaveRequest) {
            $notificationService = app(LeaveNotificationService::class);

            if ($leaveRequest->isDirty('status')) {
                switch ($leaveRequest->status) {
                    case 'approved':
                        $notificationService->notifyLeaveRequestApproved($leaveRequest);
                        // Update leave balance when request is approved
                        $leaveRequest->updateLeaveBalance();
                        break;
                    case 'rejected':
                        $notificationService->notifyLeaveRequestRejected(
                            $leaveRequest,
                            $leaveRequest->rejected_reason ?? 'No reason provided'
                        );
                        break;
                    case 'cancelled':
                        $notificationService->notifyLeaveRequestCancelled($leaveRequest);
                        // If previously approved, reverse the leave balance deduction
                        if ($leaveRequest->getOriginal('status') === 'approved') {
                            $leaveRequest->reverseLeaveBalance();
                        }
                        break;
                }
            }
        });
    }

    /**
     * Update leave balance when leave request is approved.
     */
    public function updateLeaveBalance(): void
    {
        $leaveBalance = LeaveBalance::findOrCreateForEmployee(
            $this->employee_id,
            $this->leave_type_id,
            $this->start_date->year
        );

        // Use the leave days from the balance
        $leaveBalance->useDays($this->total_days);
        $leaveBalance->save();
    }

    /**
     * Reverse leave balance deduction when leave request is cancelled.
     */
    public function reverseLeaveBalance(): void
    {
        $leaveBalance = LeaveBalance::findOrCreateForEmployee(
            $this->employee_id,
            $this->leave_type_id,
            $this->start_date->year
        );

        // Add adjustment to reverse the used days
        $leaveBalance->addAdjustment($this->total_days, "Reversal for cancelled leave request #{$this->id}");
        $leaveBalance->save();
    }
}

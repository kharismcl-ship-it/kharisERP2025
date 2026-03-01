<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveApprovalRequest extends Model
{
    use HasFactory;

    protected $table = 'hr_leave_approval_requests';

    protected $fillable = [
        'leave_request_id',
        'approval_level_id',
        'approver_employee_id',
        'status',
        'comments',
        'approved_at',
        'rejected_at',
        'reminder_sent_at',
        'escalated_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'escalated_at' => 'datetime',
    ];

    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function approvalLevel(): BelongsTo
    {
        return $this->belongsTo(LeaveApprovalLevel::class, 'approval_level_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function markAsApproved(?string $comments = null): void
    {
        $this->update([
            'status' => 'approved',
            'comments' => $comments,
            'approved_at' => now(),
        ]);
    }

    public function markAsRejected(?string $comments = null): void
    {
        $this->update([
            'status' => 'rejected',
            'comments' => $comments,
            'rejected_at' => now(),
        ]);
    }

    public function markReminderSent(): void
    {
        $this->update(['reminder_sent_at' => now()]);
    }

    public function markEscalated(): void
    {
        $this->update(['escalated_at' => now()]);
    }
}

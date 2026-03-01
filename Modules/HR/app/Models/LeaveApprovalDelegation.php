<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveApprovalDelegation extends Model
{
    use HasFactory;

    protected $table = 'hr_leave_approval_delegations';

    protected $fillable = [
        'approver_employee_id',
        'delegate_employee_id',
        'start_date',
        'end_date',
        'is_active',
        'reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'delegate_employee_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function isActive(): bool
    {
        return $this->is_active &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    public static function getActiveDelegationForApprover(int $approverId): ?self
    {
        return static::where('approver_employee_id', $approverId)
            ->active()
            ->first();
    }
}

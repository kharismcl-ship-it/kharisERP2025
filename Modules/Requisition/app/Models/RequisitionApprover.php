<?php

namespace Modules\Requisition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class RequisitionApprover extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'employee_id',
        'role',
        'order',
        'is_active',
        'decision',
        'decided_at',
        'comment',
        'signature',
        'approval_token',
        'token_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at'       => 'datetime',
            'is_active'        => 'boolean',
            'token_expires_at' => 'datetime',
        ];
    }

    public function generateToken(): void
    {
        $this->update([
            'approval_token'   => \Illuminate\Support\Str::random(64),
            'token_expires_at' => now()->addDays(7),
        ]);
    }

    public function isTokenValid(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isFuture();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (RequisitionApprover $approver) {
            RequisitionActivity::log(
                $approver->requisition,
                'approver_added',
                ($approver->role === 'approver' ? 'Approver' : 'Reviewer') . " added: {$approver->employee?->full_name}",
            );
        });

        static::updated(function (RequisitionApprover $approver) {
            if ($approver->isDirty('decision') && $approver->decision !== 'pending') {
                RequisitionActivity::log(
                    $approver->requisition,
                    'approver_decision',
                    "{$approver->employee?->full_name} marked as {$approver->decision}." . ($approver->comment ? " Comment: {$approver->comment}" : ''),
                );
            }
        });
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
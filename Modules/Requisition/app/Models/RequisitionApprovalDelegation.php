<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;

class RequisitionApprovalDelegation extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'requisition_approval_delegations';

    protected $fillable = [
        'company_id',
        'delegator_employee_id',
        'delegate_employee_id',
        'starts_at',
        'ends_at',
        'reason',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at'   => 'date',
            'is_active' => 'boolean',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        $today = now()->toDateString();

        return $query
            ->where('is_active', true)
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today);
    }

    // ── Static Helpers ────────────────────────────────────────────────────────

    public static function findDelegate(int $employeeId, int $companyId): ?Employee
    {
        $delegation = static::active()
            ->where('delegator_employee_id', $employeeId)
            ->where('company_id', $companyId)
            ->first();

        return $delegation?->delegate;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function delegator()
    {
        return $this->belongsTo(Employee::class, 'delegator_employee_id');
    }

    public function delegate()
    {
        return $this->belongsTo(Employee::class, 'delegate_employee_id');
    }
}
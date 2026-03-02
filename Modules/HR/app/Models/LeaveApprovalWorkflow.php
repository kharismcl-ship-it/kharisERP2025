<?php

namespace Modules\HR\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

class LeaveApprovalWorkflow extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'hr_leave_approval_workflows';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'is_active',
        'requires_all_approvals',
        'timeout_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_all_approvals' => 'boolean',
        'timeout_days' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function levels(): HasMany
    {
        return $this->hasMany(LeaveApprovalLevel::class, 'workflow_id');
    }

    public function activeLevels(): HasMany
    {
        return $this->levels()->orderBy('approval_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDefaultWorkflowForCompany(int $companyId): ?self
    {
        return static::where('company_id', $companyId)
            ->active()
            ->first();
    }
}

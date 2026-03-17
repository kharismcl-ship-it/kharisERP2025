<?php

namespace Modules\HR\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OffboardingChecklist extends Model
{
    use BelongsToCompany;

    protected $table = 'hr_offboarding_checklists';

    protected $fillable = [
        'company_id', 'employee_id', 'last_working_day', 'resignation_type', 'reason',
        'status', 'assets_returned', 'access_revoked', 'knowledge_transfer_done',
        'clearance_signed', 'final_payroll_processed', 'exit_interview_done',
        'exit_interview_notes', 'assets_notes', 'processed_by', 'completed_at',
    ];

    protected $casts = [
        'last_working_day'          => 'date',
        'completed_at'              => 'datetime',
        'assets_returned'           => 'boolean',
        'access_revoked'            => 'boolean',
        'knowledge_transfer_done'   => 'boolean',
        'clearance_signed'          => 'boolean',
        'final_payroll_processed'   => 'boolean',
        'exit_interview_done'       => 'boolean',
    ];

    const RESIGNATION_TYPES = [
        'resignation'  => 'Resignation',
        'termination'  => 'Termination',
        'retirement'   => 'Retirement',
        'redundancy'   => 'Redundancy',
    ];

    const STATUSES = [
        'initiated'   => 'Initiated',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getCompletionPercentageAttribute(): int
    {
        $items = [
            'assets_returned', 'access_revoked', 'knowledge_transfer_done',
            'clearance_signed', 'final_payroll_processed', 'exit_interview_done',
        ];
        $done = collect($items)->filter(fn ($item) => $this->$item)->count();

        return (int) round(($done / count($items)) * 100);
    }
}

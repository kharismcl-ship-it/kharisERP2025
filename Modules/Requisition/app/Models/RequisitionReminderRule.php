<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionReminderRule extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'requisition_reminder_rules';

    protected $fillable = [
        'company_id',
        'name',
        'trigger_status',
        'hours_after_trigger',
        'reminder_channels',
        'notify_requester',
        'notify_approvers',
        'escalate_urgency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'reminder_channels' => 'array',
            'notify_requester'  => 'boolean',
            'notify_approvers'  => 'boolean',
            'escalate_urgency'  => 'boolean',
            'is_active'         => 'boolean',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

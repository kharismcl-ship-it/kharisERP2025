<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmComplianceChecklist extends Model
{
    protected $table = 'farm_compliance_checklists';

    protected $fillable = [
        'company_id',
        'farm_id',
        'farm_certification_id',
        'checklist_name',
        'checklist_type',
        'items',
        'completion_pct',
        'conducted_by',
        'audit_date',
        'next_audit_date',
        'outcome',
        'notes',
    ];

    protected $casts = [
        'items'           => 'array',
        'audit_date'      => 'date',
        'next_audit_date' => 'date',
        'completion_pct'  => 'float',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function certification(): BelongsTo
    {
        return $this->belongsTo(FarmCertification::class, 'farm_certification_id');
    }

    /**
     * Calculate the completion percentage based on item statuses.
     * Items with status !== 'pending' are counted as completed.
     */
    public function calculateCompletion(): float
    {
        $items = $this->items ?? [];

        if (empty($items)) {
            return 0.0;
        }

        $completed = collect($items)->filter(fn ($item) => ($item['status'] ?? 'pending') !== 'pending')->count();

        return round(($completed / count($items)) * 100, 2);
    }
}
<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\CostCentre;

class RequisitionWorkflowRule extends Model
{
    use HasFactory;

    protected $table = 'requisition_workflow_rules';

    protected $fillable = [
        'company_id',
        'name',
        'request_type',
        'min_amount',
        'max_amount',
        'cost_centre_id',
        'approver_employee_ids',
        'approver_roles',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'approver_employee_ids' => 'array',
            'approver_roles'        => 'array',
            'min_amount'            => 'decimal:2',
            'max_amount'            => 'decimal:2',
            'is_active'             => 'boolean',
            'sort_order'            => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Return all active rules for the given requisition's company that match
     * its type, amount and cost centre, ordered by sort_order.
     */
    public static function matchingRules(Requisition $req): Collection
    {
        return static::active()
            ->where('company_id', $req->company_id)
            ->orderBy('sort_order')
            ->get()
            ->filter(function (RequisitionWorkflowRule $rule) use ($req): bool {
                // Request type filter
                if ($rule->request_type && $rule->request_type !== $req->request_type) {
                    return false;
                }

                // Cost centre filter
                if ($rule->cost_centre_id && $rule->cost_centre_id !== $req->cost_centre_id) {
                    return false;
                }

                // Amount range filter
                $amount = (float) ($req->total_estimated_cost ?? 0);

                if ($rule->min_amount !== null && $amount < (float) $rule->min_amount) {
                    return false;
                }

                if ($rule->max_amount !== null && $amount >= (float) $rule->max_amount) {
                    return false;
                }

                return true;
            });
    }
}
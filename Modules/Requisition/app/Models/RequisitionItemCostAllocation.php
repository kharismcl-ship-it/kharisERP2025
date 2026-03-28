<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\CostCentre;

class RequisitionItemCostAllocation extends Model
{
    use HasFactory;

    protected $table = 'requisition_item_cost_allocations';

    protected $fillable = [
        'requisition_item_id',
        'cost_centre_id',
        'percentage',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'amount'     => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (RequisitionItemCostAllocation $allocation) {
            // Auto-calculate amount from parent item total_cost
            $item = $allocation->requisitionItem ?? RequisitionItem::find($allocation->requisition_item_id);
            if ($item && $item->total_cost && $allocation->percentage) {
                $allocation->amount = round((float) $item->total_cost * (float) $allocation->percentage / 100, 2);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function requisitionItem()
    {
        return $this->belongsTo(RequisitionItem::class);
    }

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class);
    }
}
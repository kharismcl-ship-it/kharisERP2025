<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandedCost extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'procurement_landed_costs';

    protected $fillable = [
        'company_id',
        'goods_receipt_id',
        'reference',
        'total_freight',
        'total_duty',
        'total_insurance',
        'total_customs_fee',
        'total_other',
        'grand_total',
        'allocation_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_freight'     => 'decimal:2',
        'total_duty'        => 'decimal:2',
        'total_insurance'   => 'decimal:2',
        'total_customs_fee' => 'decimal:2',
        'total_other'       => 'decimal:2',
        'grand_total'       => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $lc) {
            $lc->grand_total = $lc->total_freight
                + $lc->total_duty
                + $lc->total_insurance
                + $lc->total_customs_fee
                + $lc->total_other;
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(LandedCostLine::class);
    }

    public function allocate(): void
    {
        $grn   = $this->goodsReceipt->load('lines.item');
        $total = (float) $this->grand_total;

        if ($total <= 0) {
            return;
        }

        $grnLines = $grn->lines->filter(fn ($l) => (float) $l->quantity_received > 0);

        // Calculate allocation basis
        $basisValues = $grnLines->mapWithKeys(function ($line) {
            $basis = match ($this->allocation_method) {
                'by_quantity' => (float) $line->quantity_received,
                default       => (float) $line->quantity_received * (float) $line->unit_price,
            };
            return [$line->id => $basis];
        });

        $basisTotal = $basisValues->sum();

        if ($basisTotal <= 0) {
            return;
        }

        // Delete existing lines
        $this->lines()->delete();

        foreach ($grnLines as $line) {
            $basis     = $basisValues[$line->id] ?? 0;
            $allocated = $basisTotal > 0 ? ($basis / $basisTotal) * $total : 0;

            LandedCostLine::create([
                'landed_cost_id'       => $this->id,
                'goods_receipt_line_id'=> $line->id,
                'item_id'              => $line->item_id,
                'allocated_amount'     => round($allocated, 2),
            ]);

            // Adjust stock average cost
            if ($line->item_id && (float) $line->quantity_received > 0) {
                $stockLevel = StockLevel::where('item_id', $line->item_id)
                    ->where('company_id', $this->company_id)
                    ->first();

                if ($stockLevel) {
                    $perUnitLanded       = $allocated / (float) $line->quantity_received;
                    $newAvgCost          = (float) $stockLevel->average_unit_cost + $perUnitLanded;
                    $stockLevel->update([
                        'average_unit_cost' => $newAvgCost,
                        'total_value'       => (float) $stockLevel->quantity_on_hand * $newAvgCost,
                    ]);
                }
            }
        }

        $this->update(['status' => 'allocated']);
    }
}
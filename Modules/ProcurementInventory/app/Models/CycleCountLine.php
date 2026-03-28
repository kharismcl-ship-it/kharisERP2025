<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CycleCountLine extends Model
{
    use HasFactory;

    protected $table = 'procurement_cycle_count_lines';

    protected $fillable = [
        'count_id',
        'item_id',
        'warehouse_id',
        'system_quantity',
        'counted_quantity',
        'variance',
        'variance_pct',
        'variance_value',
        'status',
        'notes',
    ];

    protected $casts = [
        'system_quantity'  => 'decimal:4',
        'counted_quantity' => 'decimal:4',
        'variance'         => 'decimal:4',
        'variance_pct'     => 'decimal:2',
        'variance_value'   => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $line) {
            if ($line->counted_quantity !== null) {
                $systemQty   = (float) $line->system_quantity;
                $countedQty  = (float) $line->counted_quantity;
                $line->variance = $countedQty - $systemQty;
                $line->variance_pct = $systemQty > 0
                    ? abs($line->variance) / $systemQty * 100
                    : 0;

                $avgCost = (float) optional(
                    StockLevel::where('item_id', $line->item_id)->first()
                )->average_unit_cost;

                $line->variance_value = $line->variance * $avgCost;
                $line->status         = 'counted';
            }
        });
    }

    public function cycleCount(): BelongsTo
    {
        return $this->belongsTo(CycleCount::class, 'count_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
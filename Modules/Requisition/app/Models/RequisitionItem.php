<?php

namespace Modules\Requisition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'item_id',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'fulfilled_quantity',
        'vendor_name',
        'vendor_quote_ref',
        'vendor_unit_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'           => 'decimal:3',
            'unit_cost'          => 'decimal:2',
            'total_cost'         => 'decimal:2',
            'fulfilled_quantity' => 'decimal:3',
            'vendor_unit_price'  => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (RequisitionItem $item) {
            if ($item->quantity !== null && $item->unit_cost !== null) {
                $item->total_cost = round((float) $item->quantity * (float) $item->unit_cost, 2);
            }
        });

        static::saved(function (RequisitionItem $item) {
            $action = $item->wasRecentlyCreated ? 'item_added' : 'item_updated';
            $item->requisition?->recalculateTotalFromItems();
            RequisitionActivity::log(
                $item->requisition,
                $action,
                ($item->wasRecentlyCreated ? 'Item added: ' : 'Item updated: ') . $item->description,
            );
        });

        static::deleted(function (RequisitionItem $item) {
            $item->requisition?->recalculateTotalFromItems();
            RequisitionActivity::log(
                $item->requisition,
                'item_removed',
                "Item removed: {$item->description}",
            );
        });
    }

    public function isFullyFulfilled(): bool
    {
        return (float) $this->fulfilled_quantity >= (float) $this->quantity;
    }

    public function fulfilmentPercentage(): float
    {
        if (! $this->quantity || (float) $this->quantity === 0.0) {
            return 0;
        }

        return min(100, round(((float) $this->fulfilled_quantity / (float) $this->quantity) * 100, 1));
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function procurementItem()
    {
        return $this->belongsTo(\Modules\ProcurementInventory\Models\Item::class, 'item_id');
    }
}
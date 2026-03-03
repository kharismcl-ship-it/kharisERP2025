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
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'decimal:3',
            'unit_cost'  => 'decimal:2',
            'total_cost' => 'decimal:2',
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

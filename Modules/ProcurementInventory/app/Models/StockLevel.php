<?php

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLevel extends Model
{
    use HasFactory;

    protected $table = 'stock_levels';

    protected $fillable = [
        'company_id',
        'item_id',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_on_order',
        'last_counted_at',
    ];

    protected $casts = [
        'quantity_on_hand'  => 'decimal:4',
        'quantity_reserved' => 'decimal:4',
        'quantity_on_order' => 'decimal:4',
        'last_counted_at'   => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function getAvailableQuantityAttribute(): float
    {
        return max(0, (float) $this->quantity_on_hand - (float) $this->quantity_reserved);
    }

    public function needsReorder(): bool
    {
        $reorderLevel = (float) ($this->item->reorder_level ?? 0);

        return $reorderLevel > 0 && (float) $this->quantity_on_hand <= $reorderLevel;
    }
}
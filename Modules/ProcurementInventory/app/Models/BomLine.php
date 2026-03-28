<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomLine extends Model
{
    use HasFactory;

    protected $table = 'procurement_bom_lines';

    protected $fillable = [
        'bom_id',
        'component_item_id',
        'quantity_required',
        'unit_of_measure',
        'waste_factor_pct',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:4',
        'waste_factor_pct'  => 'decimal:2',
        'sort_order'        => 'integer',
    ];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }

    public function componentItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'component_item_id');
    }

    /**
     * Effective quantity including waste factor.
     */
    public function effectiveQuantity(): float
    {
        return (float) $this->quantity_required * (1 + (float) $this->waste_factor_pct / 100);
    }
}
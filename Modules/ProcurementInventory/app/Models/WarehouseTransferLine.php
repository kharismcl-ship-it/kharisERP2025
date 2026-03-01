<?php

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseTransferLine extends Model
{
    protected $table = 'warehouse_transfer_lines';

    protected $fillable = [
        'warehouse_transfer_id',
        'item_id',
        'quantity_requested',
        'quantity_transferred',
        'notes',
    ];

    protected $casts = [
        'quantity_requested'  => 'decimal:4',
        'quantity_transferred' => 'decimal:4',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(WarehouseTransfer::class, 'warehouse_transfer_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
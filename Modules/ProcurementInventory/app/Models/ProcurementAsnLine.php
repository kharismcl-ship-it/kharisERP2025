<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementAsnLine extends Model
{
    use HasFactory;

    protected $table = 'procurement_asn_lines';

    protected $fillable = [
        'asn_id',
        'purchase_order_line_id',
        'item_id',
        'quantity_shipped',
        'lot_number',
    ];

    protected $casts = [
        'quantity_shipped' => 'decimal:4',
    ];

    public function asn(): BelongsTo
    {
        return $this->belongsTo(ProcurementAsn::class, 'asn_id');
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
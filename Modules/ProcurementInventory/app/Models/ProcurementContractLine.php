<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementContractLine extends Model
{
    use HasFactory;

    protected $table = 'procurement_contract_lines';

    protected $fillable = [
        'contract_id',
        'item_id',
        'description',
        'unit_of_measure',
        'agreed_unit_price',
        'min_quantity',
        'max_quantity',
    ];

    protected $casts = [
        'agreed_unit_price' => 'decimal:4',
        'min_quantity'      => 'decimal:4',
        'max_quantity'      => 'decimal:4',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(ProcurementContract::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
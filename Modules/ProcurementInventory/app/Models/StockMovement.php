<?php

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Concerns\BelongsToCompany;

class StockMovement extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'item_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'unit_cost',
        'total_cost',
        'reference',
        'source_type',
        'source_id',
        'user_id',
        'note',
    ];

    protected $casts = [
        'quantity'        => 'decimal:4',
        'quantity_before' => 'decimal:4',
        'quantity_after'  => 'decimal:4',
        'unit_cost'       => 'decimal:4',
        'total_cost'      => 'decimal:4',
    ];

    public const TYPES = [
        'receipt'    => 'Receipt',
        'adjustment' => 'Adjustment',
        'issue'      => 'Issue',
        'transfer'   => 'Transfer',
        'return'     => 'Return',
        'opening'    => 'Opening Balance',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
}

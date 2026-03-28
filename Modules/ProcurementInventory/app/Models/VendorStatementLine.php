<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use Illuminate\Database\Eloquent\Model;

class VendorStatementLine extends Model
{
    protected $table = 'procurement_vendor_statement_lines';

    protected $fillable = [
        'statement_id',
        'transaction_date',
        'transaction_type',
        'reference',
        'description',
        'amount',
        'matched_po_id',
        'match_status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function statement()
    {
        return $this->belongsTo(VendorStatement::class, 'statement_id');
    }

    public function matchedPo()
    {
        return $this->belongsTo(PurchaseOrder::class, 'matched_po_id');
    }
}
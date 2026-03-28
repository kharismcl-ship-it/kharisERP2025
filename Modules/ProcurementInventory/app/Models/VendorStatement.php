<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class VendorStatement extends Model
{
    protected $table = 'procurement_vendor_statements';

    protected $fillable = [
        'company_id',
        'vendor_id',
        'statement_reference',
        'statement_date',
        'period_from',
        'period_to',
        'opening_balance',
        'closing_balance',
        'total_invoiced',
        'total_paid',
        'status',
        'notes',
    ];

    protected $casts = [
        'statement_date'  => 'date',
        'period_from'     => 'date',
        'period_to'       => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_invoiced'  => 'decimal:2',
        'total_paid'      => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lines()
    {
        return $this->hasMany(VendorStatementLine::class, 'statement_id');
    }

    public function scopeReconciled($query)
    {
        return $query->where('status', 'reconciled');
    }

    public function scopeDisputed($query)
    {
        return $query->where('status', 'disputed');
    }
}
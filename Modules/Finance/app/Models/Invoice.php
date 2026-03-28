<?php

namespace Modules\Finance\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Events\InvoiceCreated;
use App\Models\Concerns\BelongsToCompany;

class Invoice extends Model
{
    use HasFactory, BelongsToCompany;

    /**
     * The attributes that are mass assignable.
     */
    public const TYPES = [
        'customer' => 'Customer Invoice (AR)',
        'vendor'   => 'Vendor Invoice (AP)',
    ];

    protected $fillable = [
        'company_id',
        'type',
        'vendor_id',
        'purchase_order_id',
        'grn_id',
        'customer_name',
        'customer_type',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'match_status',
        'match_variance_amount',
        'match_notes',
        'sub_total',
        'tax_total',
        'total',
        'hostel_id',
        'farm_id',
        'construction_project_id',
        'plant_id',
        'module',
        'entity_type',
        'entity_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sub_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected $dispatchesEvents = [
        'created' => InvoiceCreated::class,
    ];

    /**
     * Get the company that owns this invoice.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the lines for this invoice.
     */
    public function lines()
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id');
    }

    /**
     * Get the payments for this invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    /**
     * Get the documents attached to this invoice.
     */
    public function documents()
    {
        return $this->hasMany(InvoiceDocument::class, 'invoice_id');
    }

    /**
     * Get payment allocations for this invoice.
     */
    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'invoice_id');
    }

    /**
     * Total amount paid via allocations.
     */
    public function amountPaid(): float
    {
        return (float) $this->paymentAllocations()->sum('amount');
    }

    /**
     * Amount still outstanding.
     */
    public function amountOutstanding(): float
    {
        return max(0.0, (float) $this->total - $this->amountPaid());
    }

    /**
     * Whether the invoice is fully paid via allocations.
     */
    public function isPaidInFull(): bool
    {
        return $this->amountOutstanding() <= 0.01;
    }
}

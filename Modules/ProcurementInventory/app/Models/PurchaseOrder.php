<?php

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Models\Concerns\BelongsToCompany;

class PurchaseOrder extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'company_id',
        'vendor_id',
        'po_number',
        'po_date',
        'expected_delivery_date',
        'status',
        'subtotal',
        'tax_total',
        'total',
        'currency',
        'payment_terms',
        'delivery_address',
        'destination_warehouse_id',
        'notes',
        'approved_by',
        'approved_at',
        'ordered_at',
        'received_at',
        'finance_invoice_id',
        'hostel_id',
        'project_id',
        'farm_id',
        'module_tag',
        'requisition_id',
        'contract_id',
    ];

    protected $casts = [
        'po_date'               => 'date',
        'expected_delivery_date'=> 'date',
        'subtotal'              => 'decimal:2',
        'tax_total'             => 'decimal:2',
        'total'                 => 'decimal:2',
        'approved_at'           => 'datetime',
        'ordered_at'            => 'datetime',
        'received_at'           => 'datetime',
    ];

    public const STATUSES = [
        'draft'               => 'Draft',
        'submitted'           => 'Submitted',
        'approved'            => 'Approved',
        'ordered'             => 'Ordered',
        'partially_received'  => 'Partially Received',
        'received'            => 'Received',
        'closed'              => 'Closed',
        'cancelled'           => 'Cancelled',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $po) {
            if (empty($po->po_number)) {
                $po->po_number = static::generatePoNumber();
            }
        });
    }

    public static function generatePoNumber(): string
    {
        $prefix = 'PO';
        $year   = date('Y');
        $month  = date('m');
        $count  = static::where('po_number', 'like', "{$prefix}-{$year}{$month}-%")->count() + 1;

        return sprintf('%s-%s%s-%06d', $prefix, $year, $month, $count);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function recalculateTotals(): void
    {
        $this->subtotal  = $this->lines()->sum('line_total') - $this->lines()->sum('tax_amount');
        $this->tax_total = $this->lines()->sum('tax_amount');
        $this->total     = $this->lines()->sum('line_total');
        $this->save();
    }

    public function submit(): void
    {
        $this->update(['status' => 'submitted']);
    }

    public function approve(): void
    {
        $this->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }

    public function markOrdered(): void
    {
        $this->update([
            'status'     => 'ordered',
            'ordered_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        if (! in_array($this->status, ['draft', 'submitted', 'approved'])) {
            throw new \Exception("Cannot cancel a PO in status: {$this->status}");
        }
        $this->update(['status' => 'cancelled']);
    }

    public function updateReceiptStatus(): void
    {
        $totalOrdered  = $this->lines()->sum('quantity');
        $totalReceived = $this->lines()->sum('quantity_received');

        if ($totalOrdered <= 0) {
            return;
        }

        if ($totalReceived >= $totalOrdered) {
            $this->update(['status' => 'received', 'received_at' => now()]);
        } elseif ($totalReceived > 0) {
            $this->update(['status' => 'partially_received']);
        }
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'submitted';
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft' && $this->lines()->exists();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'submitted', 'approved']);
    }

    public function canReceiveGoods(): bool
    {
        return in_array($this->status, ['ordered', 'partially_received']);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'approved', 'ordered', 'partially_received']);
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(\Modules\Requisition\Models\Requisition::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(ProcurementContract::class);
    }
}

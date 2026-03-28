<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ProcurementInventory\Models\Vendor;

class RequisitionRfqBid extends Model
{
    use HasFactory;

    protected $table = 'requisition_rfq_bids';

    protected $fillable = [
        'rfq_id',
        'vendor_id',
        'vendor_contact_name',
        'quoted_amount',
        'delivery_days',
        'payment_terms',
        'notes',
        'attachments',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'attachments'  => 'array',
            'submitted_at' => 'datetime',
            'quoted_amount' => 'decimal:2',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function rfq()
    {
        return $this->belongsTo(RequisitionRfq::class, 'rfq_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
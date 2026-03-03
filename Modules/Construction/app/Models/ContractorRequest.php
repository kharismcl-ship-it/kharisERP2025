<?php

namespace Modules\Construction\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Construction\Events\ContractorRequestDecided;
use Modules\Construction\Events\ContractorRequestFulfilled;
use Modules\Construction\Events\ContractorRequestSubmitted;

class ContractorRequest extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'construction_project_id',
        'contractor_id',
        'project_phase_id',
        'request_type',
        'title',
        'description',
        'priority',
        'status',
        'requested_amount',
        'approved_amount',
        'required_by',
        'reviewed_by',
        'reviewed_at',
        'approval_notes',
        'procurement_po_id',
        'finance_invoice_id',
    ];

    protected $casts = [
        'required_by'      => 'date',
        'reviewed_at'      => 'datetime',
        'requested_amount' => 'decimal:2',
        'approved_amount'  => 'decimal:2',
    ];

    const REQUEST_TYPES = ['materials', 'funds', 'labour', 'equipment', 'support', 'other'];
    const PRIORITIES    = ['low', 'medium', 'high', 'urgent'];
    const STATUSES      = ['pending', 'under_review', 'approved', 'rejected', 'fulfilled'];

    protected static function booted(): void
    {
        static::created(function (self $request) {
            ContractorRequestSubmitted::dispatch($request);
        });

        static::updated(function (self $request) {
            if ($request->isDirty('status')) {
                if (in_array($request->status, ['approved', 'rejected'])) {
                    ContractorRequestDecided::dispatch($request, $request->status);
                }
                if ($request->status === 'fulfilled') {
                    ContractorRequestFulfilled::dispatch($request);
                }
            }
        });
    }

    public function approve(float $amount, string $notes = ''): void
    {
        $this->update([
            'status'          => 'approved',
            'approved_amount' => $amount,
            'approval_notes'  => $notes,
            'reviewed_by'     => auth()->id(),
            'reviewed_at'     => now(),
        ]);
    }

    public function reject(string $notes = ''): void
    {
        $this->update([
            'status'         => 'rejected',
            'approval_notes' => $notes,
            'reviewed_by'    => auth()->id(),
            'reviewed_at'    => now(),
        ]);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContractorRequestItem::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(\Modules\ProcurementInventory\Models\PurchaseOrder::class, 'procurement_po_id');
    }

    public function financeInvoice(): BelongsTo
    {
        return $this->belongsTo(\Modules\Finance\Models\Invoice::class, 'finance_invoice_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

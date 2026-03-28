<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\Models\Employee;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class RequisitionGrn extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'requisition_grns';

    protected $fillable = [
        'company_id',
        'grn_number',
        'requisition_id',
        'purchase_order_id',
        'received_by_employee_id',
        'received_date',
        'supplier_delivery_ref',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (RequisitionGrn $grn) {
            if (empty($grn->grn_number)) {
                $prefix = 'GRN-' . now()->format('Ym') . '-';
                $last   = static::withoutGlobalScopes()
                    ->where('grn_number', 'like', $prefix . '%')
                    ->orderByDesc('id')
                    ->first();
                $seq    = $last ? ((int) substr($last->grn_number, -5)) + 1 : 1;
                $grn->grn_number = $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Methods ────────────────────────────────────────────────────────────────

    public function accept(): void
    {
        $lines          = $this->lines;
        $allAccepted    = $lines->every(fn ($l) => (float) $l->quantity_accepted >= (float) $l->quantity_received);
        $anyAccepted    = $lines->some(fn ($l) => (float) $l->quantity_accepted > 0);

        $status = match (true) {
            $allAccepted          => 'accepted',
            $anyAccepted          => 'partially_accepted',
            default               => 'rejected',
        };

        $this->update(['status' => $status]);
        $this->updateRequisitionFulfillment();
    }

    public function updateRequisitionFulfillment(): void
    {
        foreach ($this->lines as $line) {
            if ($line->requisition_item_id) {
                $item = RequisitionItem::find($line->requisition_item_id);
                if ($item) {
                    $totalAccepted = RequisitionGrnLine::where('requisition_item_id', $item->id)
                        ->whereHas('grn', fn ($q) => $q->whereIn('status', ['accepted', 'partially_accepted']))
                        ->sum('quantity_accepted');
                    $item->withoutEvents(fn () => $item->update(['fulfilled_quantity' => $totalAccepted]));
                }
            }
        }
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function receivedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'received_by_employee_id');
    }

    public function lines()
    {
        return $this->hasMany(RequisitionGrnLine::class, 'grn_id');
    }
}
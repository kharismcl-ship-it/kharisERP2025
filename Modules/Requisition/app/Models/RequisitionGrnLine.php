<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionGrnLine extends Model
{
    use HasFactory;

    protected $table = 'requisition_grn_lines';

    protected $fillable = [
        'grn_id',
        'requisition_item_id',
        'description',
        'quantity_ordered',
        'quantity_received',
        'quantity_accepted',
        'quantity_rejected',
        'rejection_reason',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered'  => 'decimal:3',
            'quantity_received' => 'decimal:3',
            'quantity_accepted' => 'decimal:3',
            'quantity_rejected' => 'decimal:3',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (RequisitionGrnLine $line) {
            // Auto-compute accepted = received - rejected
            if ($line->quantity_received !== null && $line->quantity_rejected !== null) {
                $line->quantity_accepted = max(0, (float) $line->quantity_received - (float) $line->quantity_rejected);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function grn()
    {
        return $this->belongsTo(RequisitionGrn::class, 'grn_id');
    }

    public function requisitionItem()
    {
        return $this->belongsTo(RequisitionItem::class);
    }
}
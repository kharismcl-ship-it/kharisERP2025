<?php

declare(strict_types=1);

namespace Modules\Requisition\Models;

use App\Models\Company;
use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ProcurementInventory\Models\Vendor;

class RequisitionRfq extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'requisition_rfqs';

    protected $fillable = [
        'company_id',
        'requisition_id',
        'rfq_number',
        'title',
        'description',
        'deadline',
        'status',
        'awarded_vendor_id',
        'award_justification',
        'awarded_at',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'deadline'    => 'date',
            'awarded_at'  => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (RequisitionRfq $rfq) {
            if (empty($rfq->rfq_number)) {
                $prefix = 'RFQ-' . now()->format('Ym') . '-';
                $last   = static::withoutGlobalScopes()
                    ->where('rfq_number', 'like', $prefix . '%')
                    ->orderByDesc('id')
                    ->first();
                $seq    = $last ? ((int) substr($last->rfq_number, -5)) + 1 : 1;
                $rfq->rfq_number = $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'awarded']);
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

    public function awardedVendor()
    {
        return $this->belongsTo(Vendor::class, 'awarded_vendor_id');
    }

    public function bids()
    {
        return $this->hasMany(RequisitionRfqBid::class, 'rfq_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
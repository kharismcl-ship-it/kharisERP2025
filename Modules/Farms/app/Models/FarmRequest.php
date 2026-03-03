<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;
use Modules\Farms\Events\FarmRequestStatusChanged;

class FarmRequest extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_requests';

    protected $fillable = [
        'farm_id',
        'company_id',
        'requested_by',
        'reference',
        'request_type',
        'title',
        'description',
        'urgency',
        'status',
        'approved_by',
        'approved_at',
        'fulfilled_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'approved_at'  => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    const REQUEST_TYPES = ['materials', 'funds', 'equipment', 'services', 'labour', 'other'];
    const URGENCIES     = ['low', 'medium', 'high', 'urgent'];
    const STATUSES      = ['draft', 'submitted', 'approved', 'rejected', 'fulfilled'];

    protected static function booted(): void
    {
        static::creating(function (self $request) {
            if (empty($request->reference)) {
                $request->reference = self::generateReference();
            }
        });

        static::updated(function (self $request) {
            if ($request->isDirty('status')) {
                FarmRequestStatusChanged::dispatch($request, $request->getOriginal('status'));
            }
        });
    }

    protected static function generateReference(): string
    {
        $prefix = 'FR-' . now()->format('Ym') . '-';
        $last   = self::where('reference', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('reference');

        $seq = $last ? ((int) substr($last, -5)) + 1 : 1;

        return $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FarmRequestItem::class);
    }
}

<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmCooperativeMember extends Model
{
    protected $table = 'farm_cooperative_members';

    protected $fillable = [
        'farm_cooperative_id',
        'farm_id',
        'member_number',
        'membership_date',
        'land_area_ha',
        'share_count',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'membership_date' => 'date',
        'land_area_ha'    => 'float',
        'share_count'     => 'integer',
        'is_active'       => 'boolean',
    ];

    protected static function booted(): void
    {
        // Recalculate cooperative totals when a member is saved or deleted
        static::saved(function (self $member) {
            $member->cooperative?->recalculateTotals();
        });

        static::deleted(function (self $member) {
            $member->cooperative?->recalculateTotals();
        });
    }

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(FarmCooperative::class, 'farm_cooperative_id');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAssetTransfer extends Model
{
    protected $fillable = [
        'fixed_asset_id',
        'from_location',
        'to_location',
        'from_custodian_id',
        'to_custodian_id',
        'transfer_date',
        'transferred_by_user_id',
        'notes',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function fromCustodian(): BelongsTo
    {
        return $this->belongsTo(\Modules\HR\Models\Employee::class, 'from_custodian_id');
    }

    public function toCustodian(): BelongsTo
    {
        return $this->belongsTo(\Modules\HR\Models\Employee::class, 'to_custodian_id');
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'transferred_by_user_id');
    }
}
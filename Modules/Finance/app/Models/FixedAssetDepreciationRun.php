<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAssetDepreciationRun extends Model
{
    protected $fillable = [
        'fixed_asset_id',
        'period_end_date',
        'amount',
        'accumulated_before',
        'accumulated_after',
        'journal_entry_id',
        'posted_by_user_id',
        'notes',
    ];

    protected $casts = [
        'period_end_date'    => 'date',
        'amount'             => 'decimal:2',
        'accumulated_before' => 'decimal:2',
        'accumulated_after'  => 'decimal:2',
    ];

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'posted_by_user_id');
    }
}
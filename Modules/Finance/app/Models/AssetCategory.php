<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'depreciation_method',
        'useful_life_years',
        'residual_rate',
        'asset_account_id',
        'depreciation_account_id',
        'accumulated_depreciation_account_id',
    ];

    protected $casts = [
        'useful_life_years' => 'decimal:1',
        'residual_rate'     => 'decimal:2',
    ];

    public const DEPRECIATION_METHODS = [
        'straight_line'     => 'Straight Line',
        'declining_balance' => 'Declining Balance',
        'none'              => 'No Depreciation',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function assetAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'asset_account_id');
    }

    public function depreciationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'depreciation_account_id');
    }

    public function accumulatedDepreciationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accumulated_depreciation_account_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(FixedAsset::class, 'category_id');
    }
}

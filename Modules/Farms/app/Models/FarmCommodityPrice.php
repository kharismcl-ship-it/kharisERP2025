<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmCommodityPrice extends Model
{
    protected $table = 'farm_commodity_prices';

    protected $fillable = [
        'company_id',
        'commodity_name',
        'market_name',
        'price_per_unit',
        'unit',
        'price_date',
        'source',
        'notes',
    ];

    protected $casts = [
        'price_date'     => 'date',
        'price_per_unit' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCommodity(Builder $query, string $name): Builder
    {
        return $query->where('commodity_name', $name);
    }

    public function scopeLatestPrices(Builder $query): Builder
    {
        return $query->orderByDesc('price_date');
    }

    public static function getLatestPrice(string $commodity, string $market): ?static
    {
        return static::where('commodity_name', $commodity)
            ->where('market_name', $market)
            ->orderByDesc('price_date')
            ->first();
    }
}

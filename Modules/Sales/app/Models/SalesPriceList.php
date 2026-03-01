<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesPriceList extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'currency',
        'valid_from',
        'valid_to',
        'is_default',
    ];

    protected $casts = [
        'valid_from'  => 'date',
        'valid_to'    => 'date',
        'is_default'  => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesPriceListItem::class, 'price_list_id');
    }

    public function isActive(): bool
    {
        $now = now()->toDateString();

        if ($this->valid_from && $this->valid_from->toDateString() > $now) {
            return false;
        }

        if ($this->valid_to && $this->valid_to->toDateString() < $now) {
            return false;
        }

        return true;
    }
}
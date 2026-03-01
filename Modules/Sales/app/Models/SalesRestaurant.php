<?php

namespace Modules\Sales\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesRestaurant extends Model
{
    protected $table = 'sales_restaurants';

    protected $fillable = [
        'company_id',
        'name',
        'address',
        'default_vat_rate',
        'receipt_header',
        'is_active',
    ];

    protected $casts = [
        'default_vat_rate' => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(DiningTable::class, 'restaurant_id');
    }

    public function availableTables(): HasMany
    {
        return $this->tables()->where('status', 'available');
    }
}
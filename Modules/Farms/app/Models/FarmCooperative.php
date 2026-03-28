<?php

namespace Modules\Farms\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmCooperative extends Model
{
    protected $table = 'farm_cooperatives';

    protected $fillable = [
        'company_id',
        'name',
        'registration_number',
        'type',
        'contact_person',
        'contact_phone',
        'contact_email',
        'address',
        'total_members',
        'total_land_ha',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_members' => 'integer',
        'total_land_ha' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(FarmCooperativeMember::class);
    }

    public function growerPayments(): HasMany
    {
        return $this->hasMany(FarmGrowerPayment::class);
    }

    /**
     * Recalculate totals from active members.
     */
    public function recalculateTotals(): void
    {
        $activeMembers = $this->members()->where('is_active', true)->get();

        $this->update([
            'total_members' => $activeMembers->count(),
            'total_land_ha' => $activeMembers->sum('land_area_ha'),
        ]);
    }
}
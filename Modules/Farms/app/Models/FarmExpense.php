<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;
use Modules\Farms\Events\FarmExpenseRecorded;

class FarmExpense extends Model
{
    protected $fillable = [
        'farm_id',
        'crop_cycle_id',
        'company_id',
        'expense_date',
        'category',
        'description',
        'amount',
        'supplier',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    const CATEGORIES = ['seeds', 'fertilizer', 'pesticides', 'labour', 'equipment', 'irrigation', 'other'];

    protected static function booted(): void
    {
        static::created(function (self $expense) {
            FarmExpenseRecorded::dispatch($expense);
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company;

class PublicHoliday extends Model
{
    protected $table = 'hr_public_holidays';

    protected $fillable = [
        'company_id', 'name', 'date', 'description', 'is_recurring_annually',
    ];

    protected $casts = [
        'date'                   => 'date',
        'is_recurring_annually'  => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
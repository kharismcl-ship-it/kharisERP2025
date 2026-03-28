<?php

namespace Modules\Finance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FxRate extends Model
{
    use HasFactory;

    protected $table = 'fin_fx_rates';

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'effective_date',
        'created_by_user_id',
    ];

    protected $casts = [
        'rate'           => 'decimal:6',
        'effective_date' => 'date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public static function getRate(string $fromCode, string $toCode, string $date): ?float
    {
        $rate = static::where('from_currency', $fromCode)
            ->where('to_currency', $toCode)
            ->where('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->value('rate');

        return $rate !== null ? (float) $rate : null;
    }
}
<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorScorecard extends Model
{
    use HasFactory;

    protected $table = 'procurement_vendor_scorecards';

    protected $fillable = [
        'company_id',
        'vendor_id',
        'period_year',
        'period_month',
        'total_orders',
        'on_time_rate',
        'avg_quality_rate',
        'avg_price_variance_pct',
        'overall_score',
    ];

    protected $casts = [
        'period_year'            => 'integer',
        'period_month'           => 'integer',
        'total_orders'           => 'integer',
        'on_time_rate'           => 'decimal:2',
        'avg_quality_rate'       => 'decimal:2',
        'avg_price_variance_pct' => 'decimal:2',
        'overall_score'          => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
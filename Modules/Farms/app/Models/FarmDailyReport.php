<?php

namespace Modules\Farms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;
use Modules\Farms\Events\FarmDailyReportSubmitted;

class FarmDailyReport extends Model
{
    use BelongsToCompany;

    protected $table = 'farm_daily_reports';

    protected $fillable = [
        'farm_id',
        'farm_worker_id',
        'company_id',
        'report_date',
        'summary',
        'activities_done',
        'issues_noted',
        'recommendations',
        'weather_observation',
        'attachments',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'attachments' => 'array',
        'reviewed_at' => 'datetime',
    ];

    const STATUSES = ['draft', 'submitted', 'reviewed'];

    protected static function booted(): void
    {
        static::updated(function (self $report) {
            if ($report->isDirty('status') && $report->status === 'submitted') {
                FarmDailyReportSubmitted::dispatch($report);
            }
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function farmWorker(): BelongsTo
    {
        return $this->belongsTo(FarmWorker::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }
}

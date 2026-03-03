<?php

namespace Modules\Construction\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Construction\Events\MonitoringReportSubmitted;

class MonitoringReport extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'site_monitor_id',
        'construction_project_id',
        'project_phase_id',
        'contractor_id',
        'visit_date',
        'report_date',
        'findings',
        'recommendations',
        'compliance_score',
        'weather_conditions',
        'workers_on_site',
        'status',
        'attachment_paths',
    ];

    protected $casts = [
        'visit_date'       => 'date',
        'report_date'      => 'date',
        'attachment_paths' => 'array',
    ];

    const STATUSES = ['draft', 'submitted', 'reviewed', 'actioned'];

    protected static function booted(): void
    {
        static::updated(function (self $report) {
            if ($report->isDirty('status') && $report->status === 'submitted') {
                MonitoringReportSubmitted::dispatch($report);
            }
        });
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(SiteMonitor::class, 'site_monitor_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'project_phase_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }
}

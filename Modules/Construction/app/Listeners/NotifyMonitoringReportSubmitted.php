<?php

namespace Modules\Construction\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Construction\Events\MonitoringReportSubmitted;

class NotifyMonitoringReportSubmitted
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(MonitoringReportSubmitted $event): void
    {
        $report  = $event->report;
        $project = $report->project;
        $monitor = $report->monitor;

        $data = [
            'monitor_name'      => $monitor?->name ?? 'Monitor',
            'project_name'      => $project?->name ?? 'Unknown',
            'visit_date'        => $report->visit_date?->format('d M Y'),
            'compliance_score'  => $report->compliance_score !== null ? $report->compliance_score . '/100' : 'N/A',
            'findings_summary'  => \Illuminate\Support\Str::limit($report->findings, 200),
        ];

        try {
            $this->comms->sendToModel(
                $project,
                'email',
                'construction_monitoring_report_submitted',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyMonitoringReportSubmitted failed', [
                'report_id' => $report->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}

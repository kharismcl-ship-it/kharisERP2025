<?php

namespace Modules\Farms\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Events\FarmDailyReportSubmitted;

class NotifyFarmDailyReportSubmitted
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(FarmDailyReportSubmitted $event): void
    {
        $report = $event->report;
        $company = $report->company;

        if (! $company?->email) {
            return;
        }

        $data = [
            'farm_name'         => $report->farm?->name ?? 'Farm',
            'worker_name'       => $report->farmWorker?->display_name ?? 'Worker',
            'report_date'       => $report->report_date?->format('d M Y') ?? now()->format('d M Y'),
            'summary'           => $report->summary,
            'activities_done'   => $report->activities_done,
            'issues_noted'      => $report->issues_noted ?? 'None',
            'weather'           => $report->weather_observation ?? 'Not recorded',
        ];

        try {
            $this->comms->sendToContact(
                'email',
                $company->email,
                null,
                null,
                'farms_daily_report_submitted',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyFarmDailyReportSubmitted failed', [
                'report_id' => $report->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}

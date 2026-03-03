<?php

namespace Modules\Construction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Construction\Models\MonitoringReport;

class MonitoringReportSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly MonitoringReport $report) {}
}

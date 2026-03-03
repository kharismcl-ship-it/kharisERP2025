<?php

namespace Modules\Construction\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        // CommunicationCentre integration
        \Modules\Construction\Events\ProjectMilestoneCompleted::class => [
            \Modules\Construction\Listeners\SendProjectMilestoneNotification::class,
        ],
        \Modules\Construction\Events\ProjectBudgetOverrun::class => [
            \Modules\Construction\Listeners\SendProjectBudgetOverrunAlert::class,
        ],
        \Modules\Construction\Events\ProjectCompleted::class => [
            \Modules\Construction\Listeners\SendProjectCompletionNotification::class,
        ],
        // Phase 2 events
        \Modules\Construction\Events\WorkerCheckedIn::class => [
            \Modules\Construction\Listeners\NotifyWorkerCheckIn::class,
        ],
        \Modules\Construction\Events\ContractorRequestSubmitted::class => [
            \Modules\Construction\Listeners\NotifyContractorRequestSubmitted::class,
        ],
        \Modules\Construction\Events\ContractorRequestDecided::class => [
            \Modules\Construction\Listeners\NotifyContractorRequestDecision::class,
            \Modules\Construction\Listeners\AutoCreatePOOrInvoiceOnApproval::class,
        ],
        \Modules\Construction\Events\MonitoringReportSubmitted::class => [
            \Modules\Construction\Listeners\NotifyMonitoringReportSubmitted::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}

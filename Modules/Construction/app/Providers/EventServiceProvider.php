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

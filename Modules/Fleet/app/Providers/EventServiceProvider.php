<?php

namespace Modules\Fleet\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        // Fleet comms — CommunicationCentre integration
        \Modules\Fleet\Events\MaintenanceCompleted::class => [
            \Modules\Fleet\Listeners\SendMaintenanceCompletedAlert::class,
        ],
        // FuelLogged has no comms listener — only Finance GL via Finance EventServiceProvider
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

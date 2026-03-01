<?php

namespace Modules\ManufacturingWater\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        // ManufacturingWater comms — CommunicationCentre integration
        \Modules\ManufacturingWater\Events\MwDistributionCompleted::class => [
            \Modules\ManufacturingWater\Listeners\SendDistributionCompletedAlert::class,
        ],
        \Modules\ManufacturingWater\Events\MwWaterTestFailed::class => [
            \Modules\ManufacturingWater\Listeners\SendWaterTestFailureAlert::class,
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

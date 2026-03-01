<?php

namespace Modules\ManufacturingPaper\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        // ManufacturingPaper comms — CommunicationCentre integration
        \Modules\ManufacturingPaper\Events\MpBatchCompleted::class => [
            \Modules\ManufacturingPaper\Listeners\SendBatchCompletionAlert::class,
        ],
        \Modules\ManufacturingPaper\Events\MpQualityFailed::class => [
            \Modules\ManufacturingPaper\Listeners\SendQualityFailureAlert::class,
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

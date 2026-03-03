<?php

namespace Modules\ITSupport\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Modules\ITSupport\Events\ItRequestStatusChanged::class => [
            \Modules\ITSupport\Listeners\NotifyItRequestStatusChanged::class,
        ],
        \Modules\ITSupport\Events\ItTrainingInviteSent::class => [
            \Modules\ITSupport\Listeners\NotifyItTrainingInvite::class,
        ],
    ];

    protected static $shouldDiscoverEvents = true;

    protected function configureEmailVerification(): void {}
}

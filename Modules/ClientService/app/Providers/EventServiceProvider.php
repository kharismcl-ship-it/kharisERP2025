<?php

namespace Modules\ClientService\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Modules\ClientService\Events\VisitorCheckedOut::class => [
            \Modules\ClientService\Listeners\SendVisitorCheckoutMessage::class,
        ],
    ];

    protected static $shouldDiscoverEvents = true;

    protected function configureEmailVerification(): void {}
}

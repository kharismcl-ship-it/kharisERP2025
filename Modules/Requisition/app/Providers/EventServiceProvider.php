<?php

namespace Modules\Requisition\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Modules\Requisition\Events\RequisitionStatusChanged::class => [
            \Modules\Requisition\Listeners\NotifyRequisitionStatusChanged::class,
        ],
        \Modules\Requisition\Events\RequisitionShared::class => [
            \Modules\Requisition\Listeners\NotifyRequisitionShared::class,
        ],
    ];

    protected static $shouldDiscoverEvents = true;

    protected function configureEmailVerification(): void {}
}

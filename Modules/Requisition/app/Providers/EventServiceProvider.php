<?php

namespace Modules\Requisition\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Modules\Requisition\Events\RequisitionStatusChanged::class => [
            \Modules\Requisition\Listeners\NotifyRequisitionStatusChanged::class,
            \Modules\Requisition\Listeners\AutoCreatePurchaseOrderOnApproval::class,
        ],
        \Modules\Requisition\Events\RequisitionShared::class => [
            \Modules\Requisition\Listeners\NotifyRequisitionShared::class,
        ],
        \Modules\Requisition\Events\RequisitionPartyAdded::class => [
            \Modules\Requisition\Listeners\NotifyRequisitionPartyAdded::class,
        ],
    ];

    protected static $shouldDiscoverEvents = true;

    protected function configureEmailVerification(): void {}
}
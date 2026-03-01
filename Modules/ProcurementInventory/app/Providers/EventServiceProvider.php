<?php

namespace Modules\ProcurementInventory\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        \Modules\Fleet\Events\MaintenancePartsRequested::class => [
            \Modules\ProcurementInventory\Listeners\CreateMaintenancePartsPO::class,
        ],
        \Modules\Farms\Events\CropCycleStarted::class => [
            \Modules\ProcurementInventory\Listeners\CreateCropCycleInputPO::class,
        ],
        \Modules\Construction\Events\ProjectPhaseApproved::class => [
            \Modules\ProcurementInventory\Listeners\CreateProjectMaterialsPO::class,
        ],
        \Modules\HR\Events\NewEmployeeOnboarded::class => [
            \Modules\ProcurementInventory\Listeners\CreateOnboardingItemsPO::class,
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

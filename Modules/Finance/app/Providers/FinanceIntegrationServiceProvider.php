<?php

namespace Modules\Finance\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Finance\Services\IntegrationService;

class FinanceIntegrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the integration service
        $this->app->singleton(IntegrationService::class, function ($app) {
            return new IntegrationService;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Bootstrapping logic here
    }
}

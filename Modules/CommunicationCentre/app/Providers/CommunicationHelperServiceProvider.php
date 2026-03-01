<?php

namespace Modules\CommunicationCentre\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\CommunicationCentre\Channels\CommunicationChannel;

class CommunicationHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the helper functions
        require_once __DIR__.'/../../helpers.php';
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the notification channel
        $this->app->singleton(CommunicationChannel::class, function ($app) {
            return new CommunicationChannel($app->make('Modules\CommunicationCentre\Services\CommunicationHelper'));
        });
    }
}

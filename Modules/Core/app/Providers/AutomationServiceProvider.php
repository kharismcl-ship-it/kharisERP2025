<?php

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Console\Commands\ProcessAutomations;

class AutomationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton('automation.service', function ($app) {
            return $app->make('Modules\Core\Services\AutomationService');
        });

        // Register automation handlers
        $this->registerAutomationHandlers();
    }

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerSchedules();
    }

    /**
     * Register commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ProcessAutomations::class,
            ]);
        }
    }

    /**
     * Register scheduled tasks.
     */
    protected function registerSchedules(): void
    {
        // Run automation processing every minute
        Schedule::command('automations:process')->everyMinute();

        // Additional scheduled tasks for specific automation types
        Schedule::command('automations:process --type=leave-accrual')->dailyAt('00:01');
        Schedule::command('automations:process --type=invoice-generation')->monthlyOn(1, '02:00');
        Schedule::command('automations:process --type=maintenance-reminders')->dailyAt('06:00');
    }

    /**
     * Register automation handlers from all modules.
     */
    protected function registerAutomationHandlers(): void
    {
        $handlers = [
            'leave-accrual' => 'Modules\HR\Services\Automation\LeaveAccrualHandler',
            // Add more handlers as modules implement them:
            // 'invoice-generation' => 'Modules\Finance\Services\Automation\InvoiceGenerationHandler',
            // 'maintenance-reminders' => 'Modules\Hostels\Services\Automation\MaintenanceReminderHandler',
        ];

        foreach ($handlers as $type => $handlerClass) {
            if (class_exists($handlerClass)) {
                $this->app->bind("automation.handler.{$type}", function ($app) use ($handlerClass) {
                    return $app->make($handlerClass);
                });
            }
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'automation.service',
            ProcessAutomations::class,
        ];
    }
}

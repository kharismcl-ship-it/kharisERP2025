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
        Schedule::command('automations:process --type=invoice-generation')->dailyAt('02:00');
        Schedule::command('automations:process --type=maintenance-reminder')->dailyAt('06:00');
        Schedule::command('automations:process --type=billing-cycle-generation')->dailyAt('03:00');
        Schedule::command('automations:process --type=deposit-reminder')->dailyAt('08:00');
        Schedule::command('automations:process --type=overdue-charge-reminder')->dailyAt('09:00');
    }

    /**
     * Register automation handlers from all modules.
     */
    protected function registerAutomationHandlers(): void
    {
        $handlers = [
            'leave-accrual'             => 'Modules\HR\Services\Automation\LeaveAccrualHandler',
            'invoice-generation'        => 'Modules\Finance\Services\Automation\InvoiceGenerationHandler',
            'maintenance-reminder'      => 'Modules\Fleet\Services\Automation\MaintenanceReminderHandler',
            'billing-cycle-generation'  => 'Modules\Hostels\Services\Automation\BillingCycleGenerationHandler',
            'deposit-reminder'          => 'Modules\Hostels\Services\Automation\DepositReminderHandler',
            'overdue-charge-reminder'   => 'Modules\Hostels\Services\Automation\OverdueChargeReminderHandler',
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

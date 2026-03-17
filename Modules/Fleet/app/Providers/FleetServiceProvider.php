<?php

namespace Modules\Fleet\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Fleet\Console\Commands\FleetDocumentExpiryAlertCommand;
use Modules\Fleet\Console\Commands\FleetServiceDueAlertCommand;
use Modules\Fleet\Models\DriverAssignment;
use Modules\Fleet\Models\FuelLog;
use Modules\Fleet\Models\MaintenanceRecord;
use Modules\Fleet\Models\TripLog;
use Modules\Fleet\Models\Vehicle;
use Modules\Fleet\Policies\DriverAssignmentPolicy;
use Modules\Fleet\Policies\FuelLogPolicy;
use Modules\Fleet\Policies\MaintenanceRecordPolicy;
use Modules\Fleet\Policies\TripLogPolicy;
use Modules\Fleet\Policies\VehiclePolicy;
use Modules\Fleet\Services\FleetService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FleetServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Fleet';

    protected string $nameLower = 'fleet';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // If the module is disabled, bail early.
        if (function_exists('module')) {
            $mod = module($this->name, true);
            if ($mod && method_exists($mod, 'isEnabled') && ! $mod->isEnabled()) {
                return;
            }
        }

        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->registerPolicies();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->singleton(FleetService::class);
    }

    protected function registerPolicies(): void
    {
        $policiesPath = module_path($this->name, 'app/Policies');
        if (! is_dir($policiesPath)) {
            return;
        }
        foreach (glob($policiesPath . DIRECTORY_SEPARATOR . '*.php') as $file) {
            $policyBaseName = basename($file, '.php');
            $policyClass    = "Modules\\{$this->name}\\Policies\\{$policyBaseName}";
            $modelClass     = "Modules\\{$this->name}\\Models\\" . str_replace('Policy', '', $policyBaseName);
            if (class_exists($policyClass) && class_exists($modelClass)) {
                Gate::policy($modelClass, $policyClass);
            }
        }
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            FleetDocumentExpiryAlertCommand::class,
            FleetServiceDueAlertCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('fleet:document-expiry-alert')->dailyAt('08:00');
            $schedule->command('fleet:service-due-alert')->dailyAt('08:30');
        });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\'.$this->name.'\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}

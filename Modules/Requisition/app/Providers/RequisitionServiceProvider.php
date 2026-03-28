<?php

namespace Modules\Requisition\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RequisitionServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Requisition';

    protected string $nameLower = 'requisition';

    public function boot(): void
    {
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
                \Illuminate\Support\Facades\Gate::policy($modelClass, $policyClass);
            }
        }
    }

    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Requisition\Console\Commands\EscalateOverdueRequisitionsCommand::class,
            \Modules\Requisition\Console\Commands\ProcessRequisitionSchedulesCommand::class,
            \Modules\Requisition\Console\Commands\SendRequisitionRemindersCommand::class,
        ]);
    }

    protected function registerCommandSchedules(): void
    {
        $this->callAfterResolving(\Illuminate\Console\Scheduling\Schedule::class, function (\Illuminate\Console\Scheduling\Schedule $schedule) {
            $schedule->command('requisition:escalate-overdue')->dailyAt('06:00');
            $schedule->command('requisition:process-schedules')->dailyAt('07:00');
            $schedule->command('requisition:send-reminders')->hourly();
        });
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config     = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments   = explode('.', $this->nameLower . '.' . $config_key);

                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    public function registerViews(): void
    {
        $viewPath   = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace') . '\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }
}

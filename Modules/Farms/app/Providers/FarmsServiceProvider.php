<?php

namespace Modules\Farms\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Farms\Models\CropActivity;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\FarmBudget;
use Modules\Farms\Models\FarmSale;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\FarmWorker;
use Modules\Farms\Models\CropInputApplication;
use Modules\Farms\Models\CropScoutingRecord;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\FarmPlot;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\LivestockBatch;
use Modules\Farms\Models\LivestockFeedRecord;
use Modules\Farms\Models\LivestockHealthRecord;
use Modules\Farms\Models\LivestockMortalityLog;
use Modules\Farms\Models\LivestockWeightRecord;
use Modules\Farms\Policies\CropActivityPolicy;
use Modules\Farms\Policies\CropCyclePolicy;
use Modules\Farms\Policies\FarmBudgetPolicy;
use Modules\Farms\Policies\FarmSalePolicy;
use Modules\Farms\Policies\FarmTaskPolicy;
use Modules\Farms\Policies\FarmWorkerPolicy;
use Modules\Farms\Policies\CropInputApplicationPolicy;
use Modules\Farms\Policies\CropScoutingRecordPolicy;
use Modules\Farms\Policies\FarmExpensePolicy;
use Modules\Farms\Policies\FarmPlotPolicy;
use Modules\Farms\Policies\FarmPolicy;
use Modules\Farms\Policies\HarvestRecordPolicy;
use Modules\Farms\Policies\LivestockBatchPolicy;
use Modules\Farms\Policies\LivestockFeedRecordPolicy;
use Modules\Farms\Policies\LivestockHealthRecordPolicy;
use Modules\Farms\Policies\LivestockMortalityLogPolicy;
use Modules\Farms\Policies\LivestockWeightRecordPolicy;
use Modules\Farms\Services\FarmService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FarmsServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Farms';

    protected string $nameLower = 'farms';

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
        $this->app->singleton(FarmService::class);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Farm::class, FarmPolicy::class);
        Gate::policy(FarmPlot::class, FarmPlotPolicy::class);
        Gate::policy(CropCycle::class, CropCyclePolicy::class);
        Gate::policy(LivestockBatch::class, LivestockBatchPolicy::class);
        Gate::policy(HarvestRecord::class, HarvestRecordPolicy::class);
        Gate::policy(FarmExpense::class, FarmExpensePolicy::class);
        // Phase 2 — livestock detail records
        Gate::policy(LivestockHealthRecord::class, LivestockHealthRecordPolicy::class);
        Gate::policy(LivestockWeightRecord::class, LivestockWeightRecordPolicy::class);
        Gate::policy(LivestockFeedRecord::class, LivestockFeedRecordPolicy::class);
        Gate::policy(LivestockMortalityLog::class, LivestockMortalityLogPolicy::class);
        // Phase 3 — crop detail records
        Gate::policy(CropActivity::class, CropActivityPolicy::class);
        Gate::policy(CropInputApplication::class, CropInputApplicationPolicy::class);
        Gate::policy(CropScoutingRecord::class, CropScoutingRecordPolicy::class);
        // Phase 4 — farm tasks & HR workers
        Gate::policy(FarmWorker::class, FarmWorkerPolicy::class);
        Gate::policy(FarmTask::class, FarmTaskPolicy::class);
        // Phase 5 — financial integration
        Gate::policy(FarmSale::class, FarmSalePolicy::class);
        Gate::policy(FarmBudget::class, FarmBudgetPolicy::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Farms\Console\Commands\FarmsHarvestDueAlertCommand::class,
            \Modules\Farms\Console\Commands\FarmsLivestockHealthReminderCommand::class,
            \Modules\Farms\Console\Commands\FarmsTaskOverdueAlertCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            // Run harvest due alerts daily at 7am
            $schedule->command('farms:harvest-due-alerts')->dailyAt('07:00');
            // Run livestock health reminders daily at 7:30am
            $schedule->command('farms:livestock-health-reminders')->dailyAt('07:30');
            // Run task overdue alerts daily at 8:00am
            $schedule->command('farms:task-overdue-alerts')->dailyAt('08:00');
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

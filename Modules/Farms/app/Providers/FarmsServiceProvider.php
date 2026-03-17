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
use Modules\Farms\Models\CropVariety;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmEquipment;
use Modules\Farms\Models\FarmProduceInventory;
use Modules\Farms\Models\FarmWeatherLog;
use Modules\Farms\Models\LivestockEvent;
use Modules\Farms\Models\SoilTestRecord;
use Modules\Farms\Models\FarmExpense;
use Modules\Farms\Models\FarmPlot;
use Modules\Farms\Models\HarvestRecord;
use Modules\Farms\Models\LivestockBatch;
use Modules\Farms\Models\LivestockFeedRecord;
use Modules\Farms\Models\LivestockHealthRecord;
use Modules\Farms\Models\LivestockMortalityLog;
use Modules\Farms\Models\LivestockWeightRecord;
// Phase 2 — new models
use Modules\Farms\Models\FarmWorkerAttendance;
use Modules\Farms\Models\FarmDailyReport;
use Modules\Farms\Models\FarmDocument;
use Modules\Farms\Models\FarmRequest;
use Modules\Farms\Models\FarmRequestItem;
use Modules\Farms\Models\FarmSeason;
use Modules\Farms\Models\FarmMilestone;
use Modules\Farms\Policies\CropActivityPolicy;
use Modules\Farms\Policies\CropCyclePolicy;
use Modules\Farms\Policies\FarmBudgetPolicy;
use Modules\Farms\Policies\FarmEquipmentPolicy;
use Modules\Farms\Policies\FarmProduceInventoryPolicy;
use Modules\Farms\Policies\FarmSalePolicy;
use Modules\Farms\Policies\FarmTaskPolicy;
use Modules\Farms\Policies\FarmWeatherLogPolicy;
use Modules\Farms\Policies\FarmWorkerPolicy;
use Modules\Farms\Policies\CropInputApplicationPolicy;
use Modules\Farms\Policies\CropScoutingRecordPolicy;
use Modules\Farms\Policies\CropVarietyPolicy;
use Modules\Farms\Policies\LivestockEventPolicy;
use Modules\Farms\Policies\SoilTestRecordPolicy;
use Modules\Farms\Policies\FarmExpensePolicy;
use Modules\Farms\Policies\FarmPlotPolicy;
use Modules\Farms\Policies\FarmPolicy;
use Modules\Farms\Policies\HarvestRecordPolicy;
use Modules\Farms\Policies\LivestockBatchPolicy;
use Modules\Farms\Policies\LivestockFeedRecordPolicy;
use Modules\Farms\Policies\LivestockHealthRecordPolicy;
use Modules\Farms\Policies\LivestockMortalityLogPolicy;
use Modules\Farms\Policies\LivestockWeightRecordPolicy;
// Phase 2 — new policies
use Modules\Farms\Policies\FarmWorkerAttendancePolicy;
use Modules\Farms\Policies\FarmDailyReportPolicy;
use Modules\Farms\Policies\FarmDocumentPolicy;
use Modules\Farms\Policies\FarmRequestPolicy;
use Modules\Farms\Policies\FarmRequestItemPolicy;
use Modules\Farms\Policies\FarmSeasonPolicy;
use Modules\Farms\Policies\FarmMilestonePolicy;
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

        $this->registerTripLogObserver();
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->registerPolicies();
        $this->registerLivewireComponents();
    }

    protected function registerTripLogObserver(): void
    {
        // When a Fleet TripLog is marked completed, auto-mark the linked FarmOrder as delivered
        try {
            \Modules\Fleet\Models\TripLog::saved(function (\Modules\Fleet\Models\TripLog $trip) {
                if ($trip->status === 'completed' && $trip->isDirty('status')) {
                    $delivery = \Modules\Farms\Models\FarmOrderDelivery::where('trip_log_id', $trip->id)->first();
                    if ($delivery) {
                        $delivery->update(['status' => 'delivered', 'delivered_at' => now()]);
                        $delivery->farmOrder?->update(['status' => 'delivered']);
                    }
                }
            });
        } catch (\Throwable) {
            // Fleet module may not be loaded — silently skip
        }
    }

    protected function registerLivewireComponents(): void
    {
        // Filament widgets
        \Livewire\Livewire::component('modules.farms.filament.widgets.farm-dashboard-stats-widget', \Modules\Farms\Filament\Widgets\FarmDashboardStatsWidget::class);
        \Livewire\Livewire::component('modules.farms.filament.widgets.farm-financial-report-stats-widget', \Modules\Farms\Filament\Widgets\FarmFinancialReportStatsWidget::class);

        \Livewire\Livewire::component('farms::farm-index', \Modules\Farms\Http\Livewire\FarmIndex::class);
        \Livewire\Livewire::component('farms::farm-dashboard', \Modules\Farms\Http\Livewire\FarmDashboard::class);
        \Livewire\Livewire::component('farms::navigation', \Modules\Farms\Http\Livewire\Navigation::class);
        \Livewire\Livewire::component('farms::farm-map', \Modules\Farms\Http\Livewire\FarmMap::class);
        \Livewire\Livewire::component('farms::tasks.index', \Modules\Farms\Http\Livewire\Tasks\Index::class);
        \Livewire\Livewire::component('farms::daily-reports.index', \Modules\Farms\Http\Livewire\DailyReports\Index::class);
        \Livewire\Livewire::component('farms::daily-reports.create', \Modules\Farms\Http\Livewire\DailyReports\Create::class);
        \Livewire\Livewire::component('farms::daily-reports.show', \Modules\Farms\Http\Livewire\DailyReports\Show::class);
        \Livewire\Livewire::component('farms::crops.index', \Modules\Farms\Http\Livewire\Crops\Index::class);
        \Livewire\Livewire::component('farms::crops.show', \Modules\Farms\Http\Livewire\Crops\Show::class);
        \Livewire\Livewire::component('farms::crops.record-harvest', \Modules\Farms\Http\Livewire\Crops\RecordHarvest::class);
        \Livewire\Livewire::component('farms::livestock.index', \Modules\Farms\Http\Livewire\Livestock\Index::class);
        \Livewire\Livewire::component('farms::livestock.show', \Modules\Farms\Http\Livewire\Livestock\Show::class);
        \Livewire\Livewire::component('farms::requests.index', \Modules\Farms\Http\Livewire\Requests\Index::class);
        \Livewire\Livewire::component('farms::requests.create', \Modules\Farms\Http\Livewire\Requests\Create::class);
        \Livewire\Livewire::component('farms::requests.show', \Modules\Farms\Http\Livewire\Requests\Show::class);
        \Livewire\Livewire::component('farms::attendance.index', \Modules\Farms\Http\Livewire\Attendance\Index::class);
        \Livewire\Livewire::component('farms::reports.index', \Modules\Farms\Http\Livewire\Reports\Index::class);

        // Public Farm Shop
        \Livewire\Livewire::component('farms::shop.index', \Modules\Farms\Http\Livewire\Shop\Index::class);
        \Livewire\Livewire::component('farms::shop.show', \Modules\Farms\Http\Livewire\Shop\Show::class);
        \Livewire\Livewire::component('farms::shop.cart', \Modules\Farms\Http\Livewire\Shop\Cart::class);
        \Livewire\Livewire::component('farms::shop.checkout', \Modules\Farms\Http\Livewire\Shop\Checkout::class);
        \Livewire\Livewire::component('farms::shop.order-payment', \Modules\Farms\Http\Livewire\Shop\OrderPayment::class);
        \Livewire\Livewire::component('farms::shop.order-payment-return', \Modules\Farms\Http\Livewire\Shop\OrderPaymentReturn::class);
        \Livewire\Livewire::component('farms::shop.order-confirmation', \Modules\Farms\Http\Livewire\Shop\OrderConfirmation::class);
        \Livewire\Livewire::component('farms::shop.order-tracking', \Modules\Farms\Http\Livewire\Shop\OrderTracking::class);
        \Livewire\Livewire::component('farms::shop.my-orders', \Modules\Farms\Http\Livewire\Shop\MyOrders::class);
        \Livewire\Livewire::component('farms::shop.auth.login', \Modules\Farms\Http\Livewire\Shop\Auth\Login::class);
        \Livewire\Livewire::component('farms::shop.auth.register', \Modules\Farms\Http\Livewire\Shop\Auth\Register::class);
        \Livewire\Livewire::component('farms::shop.auth.forgot-password', \Modules\Farms\Http\Livewire\Shop\Auth\ForgotPassword::class);
        \Livewire\Livewire::component('farms::shop.auth.reset-password', \Modules\Farms\Http\Livewire\Shop\Auth\ResetPassword::class);
        \Livewire\Livewire::component('farms::shop.my-profile', \Modules\Farms\Http\Livewire\Shop\MyProfile::class);
        \Livewire\Livewire::component('farms::shop.my-wishlist', \Modules\Farms\Http\Livewire\Shop\MyWishlist::class);
        \Livewire\Livewire::component('farms::shop.order-receipt', \Modules\Farms\Http\Livewire\Shop\OrderReceipt::class);
        \Livewire\Livewire::component('farms::shop.request-refund', \Modules\Farms\Http\Livewire\Shop\RequestRefund::class);
        \Livewire\Livewire::component('farms::shop.harvest-calendar', \Modules\Farms\Http\Livewire\Shop\HarvestCalendar::class);
        \Livewire\Livewire::component('farms::shop.my-subscriptions', \Modules\Farms\Http\Livewire\Shop\MySubscriptions::class);
        \Livewire\Livewire::component('farms::shop.bundle-show', \Modules\Farms\Http\Livewire\Shop\BundleShow::class);
        \Livewire\Livewire::component('farms::shop.farm-shop-page', \Modules\Farms\Http\Livewire\Shop\FarmShopPage::class);
        \Livewire\Livewire::component('farms::shop.blog-index', \Modules\Farms\Http\Livewire\Shop\BlogIndex::class);
        \Livewire\Livewire::component('farms::shop.blog-show', \Modules\Farms\Http\Livewire\Shop\BlogShow::class);
        \Livewire\Livewire::component('farms::shop.farm-profile', \Modules\Farms\Http\Livewire\Shop\FarmProfile::class);
        \Livewire\Livewire::component('farms::shop.b2b-register', \Modules\Farms\Http\Livewire\Shop\B2bRegister::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->singleton(FarmService::class);
        $this->app->singleton(\Modules\Farms\Services\ShopSettingsService::class);
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
        // Phase 6 — Farmbrite parity features
        Gate::policy(FarmProduceInventory::class, FarmProduceInventoryPolicy::class);
        Gate::policy(LivestockEvent::class, LivestockEventPolicy::class);
        Gate::policy(FarmEquipment::class, FarmEquipmentPolicy::class);
        Gate::policy(FarmWeatherLog::class, FarmWeatherLogPolicy::class);
        Gate::policy(SoilTestRecord::class, SoilTestRecordPolicy::class);
        Gate::policy(CropVariety::class, CropVarietyPolicy::class);
        // Phase 2 — new model policies
        Gate::policy(FarmWorkerAttendance::class, FarmWorkerAttendancePolicy::class);
        Gate::policy(FarmDailyReport::class, FarmDailyReportPolicy::class);
        Gate::policy(FarmDocument::class, FarmDocumentPolicy::class);
        Gate::policy(FarmRequest::class, FarmRequestPolicy::class);
        Gate::policy(FarmRequestItem::class, FarmRequestItemPolicy::class);
        Gate::policy(FarmSeason::class, FarmSeasonPolicy::class);
        Gate::policy(FarmMilestone::class, FarmMilestonePolicy::class);
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
            \Modules\Farms\Console\Commands\FarmsNotifyRestockCommand::class,
            \Modules\Farms\Console\Commands\FarmsAbandonedCartRecoveryCommand::class,
            \Modules\Farms\Console\Commands\FarmsProcessSubscriptionsCommand::class,
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
            // Restock notifications — check every hour
            $schedule->command('farms:notify-restock')->hourly();
            // Abandoned cart recovery — check every 30 minutes
            $schedule->command('farms:abandoned-cart-recovery')->everyThirtyMinutes();
            // Subscription order generation — daily at 6am
            $schedule->command('farms:process-subscriptions')->dailyAt('06:00');
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

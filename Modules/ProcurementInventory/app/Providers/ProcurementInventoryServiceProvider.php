<?php

namespace Modules\ProcurementInventory\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Models\Item;
use Modules\ProcurementInventory\Models\ItemCategory;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\StockLevel;
use Modules\ProcurementInventory\Models\Vendor;
use Modules\ProcurementInventory\Models\Warehouse;
use Modules\ProcurementInventory\Models\WarehouseTransfer;
use Illuminate\Console\Scheduling\Schedule;
use Modules\ProcurementInventory\Console\Commands\CheckProcurementContractsCommand;
use Modules\ProcurementInventory\Console\Commands\CheckVendorCertificatesCommand;
use Modules\ProcurementInventory\Console\Commands\ExpireStockLotsCommand;
use Modules\ProcurementInventory\Console\Commands\ReorderAlertCommand;
use Modules\ProcurementInventory\Models\Bom;
use Modules\ProcurementInventory\Models\CycleCount;
use Modules\ProcurementInventory\Models\InspectionLot;
use Modules\ProcurementInventory\Models\LandedCost;
use Modules\ProcurementInventory\Models\PoChangeOrder;
use Modules\ProcurementInventory\Models\ProcurementApprovalRule;
use Modules\ProcurementInventory\Models\ProcurementAsn;
use Modules\ProcurementInventory\Models\ProcurementContract;
use Modules\ProcurementInventory\Models\ProcurementInvoiceMatch;
use Modules\ProcurementInventory\Models\RtvOrder;
use Modules\ProcurementInventory\Models\StockLot;
use Modules\ProcurementInventory\Models\VendorApplication;
use Modules\ProcurementInventory\Models\VendorCatalog;
use Modules\ProcurementInventory\Models\VendorCertificate;
use Modules\ProcurementInventory\Models\VendorScorecard;
use Modules\ProcurementInventory\Policies\BomPolicy;
use Modules\ProcurementInventory\Policies\CycleCountPolicy;
use Modules\ProcurementInventory\Policies\GoodsReceiptPolicy;
use Modules\ProcurementInventory\Policies\InspectionLotPolicy;
use Modules\ProcurementInventory\Policies\ItemCategoryPolicy;
use Modules\ProcurementInventory\Policies\ItemPolicy;
use Modules\ProcurementInventory\Policies\LandedCostPolicy;
use Modules\ProcurementInventory\Policies\PoChangeOrderPolicy;
use Modules\ProcurementInventory\Policies\ProcurementApprovalRulePolicy;
use Modules\ProcurementInventory\Policies\ProcurementAsnPolicy;
use Modules\ProcurementInventory\Policies\ProcurementContractPolicy;
use Modules\ProcurementInventory\Policies\ProcurementInvoiceMatchPolicy;
use Modules\ProcurementInventory\Policies\PurchaseOrderPolicy;
use Modules\ProcurementInventory\Policies\RtvOrderPolicy;
use Modules\ProcurementInventory\Policies\StockLevelPolicy;
use Modules\ProcurementInventory\Policies\StockLotPolicy;
use Modules\ProcurementInventory\Policies\VendorApplicationPolicy;
use Modules\ProcurementInventory\Policies\VendorCatalogPolicy;
use Modules\ProcurementInventory\Policies\VendorCertificatePolicy;
use Modules\ProcurementInventory\Policies\VendorPerformancePolicy;
use Modules\ProcurementInventory\Policies\VendorPolicy;
use Modules\ProcurementInventory\Policies\WarehousePolicy;
use Modules\ProcurementInventory\Policies\WarehouseTransferPolicy;
use Modules\ProcurementInventory\Services\CycleCountService;
use Modules\ProcurementInventory\Services\ProcurementService;
use Modules\ProcurementInventory\Services\StockService;
use Modules\ProcurementInventory\Services\VendorPerformanceService;
use Modules\ProcurementInventory\Services\WarehouseTransferService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProcurementInventoryServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'ProcurementInventory';

    protected string $nameLower = 'procurementinventory';

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
        $this->registerPolicies();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(StockService::class);
        $this->app->singleton(VendorPerformanceService::class);
        $this->app->singleton(ProcurementService::class);
        $this->app->singleton(WarehouseTransferService::class);
        $this->app->singleton(CycleCountService::class);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(ItemCategory::class, ItemCategoryPolicy::class);
        Gate::policy(Item::class, ItemPolicy::class);
        Gate::policy(Vendor::class, VendorPolicy::class);
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
        Gate::policy(GoodsReceipt::class, GoodsReceiptPolicy::class);
        Gate::policy(StockLevel::class, StockLevelPolicy::class);
        Gate::policy(Warehouse::class, WarehousePolicy::class);
        Gate::policy(WarehouseTransfer::class, WarehouseTransferPolicy::class);
        Gate::policy(ProcurementApprovalRule::class, ProcurementApprovalRulePolicy::class);
        Gate::policy(ProcurementInvoiceMatch::class, ProcurementInvoiceMatchPolicy::class);
        Gate::policy(VendorScorecard::class, VendorPerformancePolicy::class);
        Gate::policy(VendorApplication::class, VendorApplicationPolicy::class);
        Gate::policy(ProcurementContract::class, ProcurementContractPolicy::class);
        Gate::policy(VendorCatalog::class, VendorCatalogPolicy::class);
        Gate::policy(VendorCertificate::class, VendorCertificatePolicy::class);
        Gate::policy(InspectionLot::class, InspectionLotPolicy::class);
        Gate::policy(RtvOrder::class, RtvOrderPolicy::class);
        // Phase 3 & 4
        Gate::policy(StockLot::class, StockLotPolicy::class);
        Gate::policy(CycleCount::class, CycleCountPolicy::class);
        Gate::policy(LandedCost::class, LandedCostPolicy::class);
        Gate::policy(PoChangeOrder::class, PoChangeOrderPolicy::class);
        Gate::policy(ProcurementAsn::class, ProcurementAsnPolicy::class);
        Gate::policy(Bom::class, BomPolicy::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            ReorderAlertCommand::class,
            CheckVendorCertificatesCommand::class,
            CheckProcurementContractsCommand::class,
            ExpireStockLotsCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            // Daily reorder alert at 07:00
            $schedule->command('procurement:reorder-alert')->dailyAt('07:00');
            // Daily certificate expiry check at 08:00
            $schedule->command('procurement:check-certificates')->dailyAt('08:00');
            // Daily contract expiry check at 08:05
            $schedule->command('procurement:check-contracts')->dailyAt('08:05');
            // Daily expire stock lots at 06:30
            $schedule->command('procurement:expire-lots')->dailyAt('06:30');
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

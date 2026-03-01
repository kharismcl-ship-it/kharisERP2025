<?php

namespace Modules\Sales\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Sales\Models\DiningOrder;
use Modules\Sales\Models\DiningTable;
use Modules\Sales\Models\KitchenTicket;
use Modules\Sales\Models\PosSession;
use Modules\Sales\Models\PosSale;
use Modules\Sales\Models\PosTerminal;
use Modules\Sales\Models\SalesActivity;
use Modules\Sales\Models\SalesCatalog;
use Modules\Sales\Models\SalesContact;
use Modules\Sales\Models\SalesLead;
use Modules\Sales\Models\SalesOpportunity;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Models\SalesOrganization;
use Modules\Sales\Models\SalesPriceList;
use Modules\Sales\Models\SalesQuotation;
use Modules\Sales\Models\SalesRestaurant;
use Modules\Sales\Policies\DiningOrderPolicy;
use Modules\Sales\Policies\DiningTablePolicy;
use Modules\Sales\Policies\KitchenTicketPolicy;
use Modules\Sales\Policies\PosSessionPolicy;
use Modules\Sales\Policies\PosSalePolicy;
use Modules\Sales\Policies\PosTerminalPolicy;
use Modules\Sales\Policies\SalesActivityPolicy;
use Modules\Sales\Policies\SalesCatalogPolicy;
use Modules\Sales\Policies\SalesContactPolicy;
use Modules\Sales\Policies\SalesLeadPolicy;
use Modules\Sales\Policies\SalesOpportunityPolicy;
use Modules\Sales\Policies\SalesOrderPolicy;
use Modules\Sales\Policies\SalesOrganizationPolicy;
use Modules\Sales\Policies\SalesPriceListPolicy;
use Modules\Sales\Policies\SalesQuotationPolicy;
use Modules\Sales\Policies\SalesRestaurantPolicy;
use Modules\Sales\Services\CatalogSyncService;
use Modules\Sales\Services\SalesFulfillmentService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SalesServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Sales';

    protected string $nameLower = 'sales';

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

    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->singleton(CatalogSyncService::class);
        $this->app->singleton(SalesFulfillmentService::class);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(SalesLead::class, SalesLeadPolicy::class);
        Gate::policy(SalesContact::class, SalesContactPolicy::class);
        Gate::policy(SalesOrganization::class, SalesOrganizationPolicy::class);
        Gate::policy(SalesActivity::class, SalesActivityPolicy::class);
        Gate::policy(SalesCatalog::class, SalesCatalogPolicy::class);
        Gate::policy(SalesPriceList::class, SalesPriceListPolicy::class);
        Gate::policy(SalesOpportunity::class, SalesOpportunityPolicy::class);
        Gate::policy(SalesQuotation::class, SalesQuotationPolicy::class);
        Gate::policy(SalesOrder::class, SalesOrderPolicy::class);
        Gate::policy(PosTerminal::class, PosTerminalPolicy::class);
        Gate::policy(PosSession::class, PosSessionPolicy::class);
        Gate::policy(PosSale::class, PosSalePolicy::class);
        Gate::policy(SalesRestaurant::class, SalesRestaurantPolicy::class);
        Gate::policy(DiningTable::class, DiningTablePolicy::class);
        Gate::policy(DiningOrder::class, DiningOrderPolicy::class);
        Gate::policy(KitchenTicket::class, KitchenTicketPolicy::class);
    }

    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Sales\Console\Commands\SyncCatalogCommand::class,
        ]);
    }

    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('sales:sync-catalog')->everyFifteenMinutes();
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
                    $config    = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments  = explode('.', $this->nameLower . '.' . $config_key);

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

    protected function merge_config_from(string $path, string $key): void
    {
        $existing      = config($key, []);
        $module_config = require $path;
        config([$key => array_replace_recursive($existing, $module_config)]);
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
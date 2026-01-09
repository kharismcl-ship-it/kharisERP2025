<?php

namespace Modules\Hostels\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Hostels\Console\Commands\ReleaseExpiredManualBookings;
use Modules\Hostels\Console\Commands\SendCheckoutReminders;
use Modules\Hostels\Console\Commands\SendPreArrivalReminders;
use Modules\Hostels\Console\Commands\SyncHostelPayroll;
use Modules\Hostels\Console\Commands\TestCheckInProcess;
use Modules\Hostels\Console\Commands\TestCommunication;
use Modules\Hostels\Filament\Resources\HostelWhatsAppGroupResource;
use Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Observers\BookingObserver;
use Modules\Hostels\Services\HostelCommunicationService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class HostelsServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Hostels';

    protected string $nameLower = 'hostels';

    protected array $filamentResources = [
        HostelWhatsAppGroupResource::class,
        WhatsAppGroupMessageResource::class,
    ];

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // If the module is disabled, bail early.
        // This check is disabled for testing environment
        if (function_exists('module') && ! app()->environment('testing')) {
            $mod = module($this->name, true);
            if ($mod && method_exists($mod, 'isEnabled') && ! $mod->isEnabled()) {
                return;
            }
        }

        // Register the Bookingobserver
        Booking::observe(BookingObserver::class);

        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->registerLivewireComponents();
        $this->registerViewComponents();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        // Register the HostelCommunicationService
        $this->app->singleton(HostelCommunicationService::class, function ($app) {
            return new HostelCommunicationService;
        });
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            ReleaseExpiredManualBookings::class,
            SendCheckoutReminders::class,
            SendPreArrivalReminders::class,
            SyncHostelPayroll::class,
            TestCheckInProcess::class,
            TestCommunication::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            $schedule->command('hostels:release-expired-manual-bookings')->hourly();
            $schedule->command('hostels:send-checkout-reminders --days=1')->dailyAt('09:00');
            $schedule->command('hostels:send-pre-arrival-reminders')->dailyAt('08:00');
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
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
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
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        // Check if Livewire is installed
        if (! class_exists(\Livewire\Livewire::class)) {
            return;
        }

        // Register public components
        \Livewire\Livewire::component('hostels.public.index', \Modules\Hostels\Http\Livewire\Public\Index::class);
        \Livewire\Livewire::component('hostels.public.show', \Modules\Hostels\Http\Livewire\Public\Show::class);
        \Livewire\Livewire::component('hostels.public.booking-wizard', \Modules\Hostels\Http\Livewire\Public\BookingWizard::class);
        \Livewire\Livewire::component('hostels.public.booking-confirmation', \Modules\Hostels\Http\Livewire\Public\BookingConfirmation::class);
        \Livewire\Livewire::component('hostels.public.booking-payment', \Modules\Hostels\Http\Livewire\Public\BookingPayment::class);
        \Livewire\Livewire::component('hostels.public.booking-payment-return', \Modules\Hostels\Http\Livewire\Public\BookingPaymentReturn::class);
        \Livewire\Livewire::component('hostels.public.booking-payment-failed', \Modules\Hostels\Http\Livewire\Public\BookingPaymentFailed::class);

        // Register tenant components
        \Livewire\Livewire::component('hostels.tenant.navigation', \Modules\Hostels\Http\Livewire\Tenant\Navigation::class);

        // Register other components
        \Livewire\Livewire::component('hostels.bed-list', \Modules\Hostels\Http\Livewire\BedList::class);
        \Livewire\Livewire::component('hostels.booking-list', \Modules\Hostels\Http\Livewire\BookingList::class);
        \Livewire\Livewire::component('hostels.dashboard', \Modules\Hostels\Http\Livewire\Dashboard::class);
        \Livewire\Livewire::component('hostels.hostel-charge-list', \Modules\Hostels\Http\Livewire\HostelChargeList::class);
        \Livewire\Livewire::component('hostels.hostel-list', \Modules\Hostels\Http\Livewire\HostelList::class);
        \Livewire\Livewire::component('hostels.room-list', \Modules\Hostels\Http\Livewire\RoomList::class);
        \Livewire\Livewire::component('hostels.tenant-list', \Modules\Hostels\Http\Livewire\TenantList::class);

        // Register nested components
        \Livewire\Livewire::component('hostels.bookings.create', \Modules\Hostels\Http\Livewire\Bookings\Create::class);
        \Livewire\Livewire::component('hostels.bookings.show', \Modules\Hostels\Http\Livewire\Bookings\Show::class);
        \Livewire\Livewire::component('hostels.incidents.index', \Modules\Hostels\Http\Livewire\Incidents\Index::class);
        \Livewire\Livewire::component('hostels.maintenance.index', \Modules\Hostels\Http\Livewire\Maintenance\Index::class);
        \Livewire\Livewire::component('hostels.reports.index', \Modules\Hostels\Http\Livewire\Reports\Index::class);
        \Livewire\Livewire::component('hostels.visitors.index', \Modules\Hostels\Http\Livewire\Visitors\Index::class);
    }

    /**
     * Register view components.
     */
    protected function registerViewComponents(): void
    {
        // Blade::component('flux.steps.index', \Modules\Hostels\View\Components\Steps::class, 'flux::steps');
        // Blade::component('flux.steps.step', \Modules\Hostels\View\Components\Step::class, 'flux::step');
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

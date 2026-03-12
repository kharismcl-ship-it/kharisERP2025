<?php

namespace Modules\Hostels\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
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

        $this->registerPolicies();
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
     * Register module policies with the Gate so Filament and Laravel
     * respect permissions and toggles for this module's models.
     */
    protected function registerPolicies(): void
    {
        $policiesPath = module_path($this->name, 'app/Policies');

        if (! is_dir($policiesPath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($policiesPath));

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $policyClass = 'Modules\\'.$this->name.'\\Policies\\'.basename($file->getFilename(), '.php');
            if (! class_exists($policyClass)) {
                continue;
            }

            $contents = @file_get_contents($file->getPathname()) ?: '';

            if (preg_match('/use\\s+Modules\\\\'.$this->name.'\\\\Models\\\\([A-Za-z0-9_\\\\]+);/', $contents, $matches)) {
                $modelClass = 'Modules\\'.$this->name.'\\Models\\'.$matches[1];

                if (class_exists($modelClass)) {
                    Gate::policy($modelClass, $policyClass);
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

        // ── Public ────────────────────────────────────────────────────────────
        \Livewire\Livewire::component('hostels.public.index', \Modules\Hostels\Http\Livewire\Public\Index::class);
        \Livewire\Livewire::component('hostels.public.show', \Modules\Hostels\Http\Livewire\Public\Show::class);
        \Livewire\Livewire::component('hostels.public.booking-wizard', \Modules\Hostels\Http\Livewire\Public\BookingWizard::class);
        \Livewire\Livewire::component('hostels.public.booking-confirmation', \Modules\Hostels\Http\Livewire\Public\BookingConfirmation::class);
        \Livewire\Livewire::component('hostels.public.booking-payment', \Modules\Hostels\Http\Livewire\Public\BookingPayment::class);
        \Livewire\Livewire::component('hostels.public.booking-payment-return', \Modules\Hostels\Http\Livewire\Public\BookingPaymentReturn::class);
        \Livewire\Livewire::component('hostels.public.booking-payment-failed', \Modules\Hostels\Http\Livewire\Public\BookingPaymentFailed::class);
        \Livewire\Livewire::component('hostels.public.booking-change-request', \Modules\Hostels\Http\Livewire\Public\BookingChangeRequest::class);

        // ── Admin — list/widget components ───────────────────────────────────
        \Livewire\Livewire::component('hostels.admin.hostel-list', \Modules\Hostels\Http\Livewire\Admin\HostelList::class);
        \Livewire\Livewire::component('hostels.admin.dashboard', \Modules\Hostels\Http\Livewire\Admin\Dashboard::class);
        \Livewire\Livewire::component('hostels.admin.room-list', \Modules\Hostels\Http\Livewire\Admin\RoomList::class);
        \Livewire\Livewire::component('hostels.admin.bed-list', \Modules\Hostels\Http\Livewire\Admin\BedList::class);
        \Livewire\Livewire::component('hostels.admin.booking-list', \Modules\Hostels\Http\Livewire\Admin\BookingList::class);
        \Livewire\Livewire::component('hostels.admin.hostel-charge-list', \Modules\Hostels\Http\Livewire\Admin\HostelChargeList::class);
        \Livewire\Livewire::component('hostels.admin.hostel-occupant-list', \Modules\Hostels\Http\Livewire\Admin\HostelOccupantList::class);
        \Livewire\Livewire::component('hostels.admin.hostel-whatsapp-group-list', \Modules\Hostels\Http\Livewire\Admin\HostelWhatsAppGroupList::class);
        \Livewire\Livewire::component('hostels.admin.whatsapp-group-messages', \Modules\Hostels\Http\Livewire\Admin\WhatsAppGroupMessages::class);
        \Livewire\Livewire::component('hostels.admin.booking-change-requests', \Modules\Hostels\Http\Livewire\Admin\BookingChangeRequests::class);
        \Livewire\Livewire::component('hostels.admin.booking-approval', \Modules\Hostels\Http\Livewire\Admin\BookingApproval::class);

        // ── Admin — nested components ─────────────────────────────────────────
        \Livewire\Livewire::component('hostels.admin.bookings.create', \Modules\Hostels\Http\Livewire\Admin\Bookings\Create::class);
        \Livewire\Livewire::component('hostels.admin.bookings.show', \Modules\Hostels\Http\Livewire\Admin\Bookings\Show::class);
        \Livewire\Livewire::component('hostels.admin.incidents.index', \Modules\Hostels\Http\Livewire\Admin\Incidents\Index::class);
        \Livewire\Livewire::component('hostels.admin.maintenance.index', \Modules\Hostels\Http\Livewire\Admin\Maintenance\Index::class);
        \Livewire\Livewire::component('hostels.admin.reports.index', \Modules\Hostels\Http\Livewire\Admin\Reports\Index::class);
        \Livewire\Livewire::component('hostels.admin.visitors.index', \Modules\Hostels\Http\Livewire\Admin\Visitors\Index::class);

        // ── Admin — operations ────────────────────────────────────────────────
        \Livewire\Livewire::component('hostels.admin.check-in', \Modules\Hostels\Http\Livewire\Admin\CheckIn::class);
        \Livewire\Livewire::component('hostels.admin.check-out', \Modules\Hostels\Http\Livewire\Admin\CheckOut::class);
        \Livewire\Livewire::component('hostels.admin.deposit-collection', \Modules\Hostels\Http\Livewire\Admin\DepositCollection::class);

        // ── HostelOccupant — auth ─────────────────────────────────────────────
        \Livewire\Livewire::component('hostels.hostel-occupant.auth.login', \Modules\Hostels\Http\Livewire\HostelOccupant\Auth\Login::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.auth.register', \Modules\Hostels\Http\Livewire\HostelOccupant\Auth\Register::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.auth.forgot-password', \Modules\Hostels\Http\Livewire\HostelOccupant\Auth\ForgotPassword::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.auth.reset-password', \Modules\Hostels\Http\Livewire\HostelOccupant\Auth\ResetPassword::class);

        // ── HostelOccupant — navigation ───────────────────────────────────────
        \Livewire\Livewire::component('hostels.hostel-occupant.navigation', \Modules\Hostels\Http\Livewire\HostelOccupant\Navigation::class);

        // ── HostelOccupant — portal pages ─────────────────────────────────────
        \Livewire\Livewire::component('hostels.hostel-occupant.dashboard', \Modules\Hostels\Http\Livewire\HostelOccupant\Dashboard::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.bookings.index', \Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Index::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.bookings.show', \Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Show::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.bookings.create', \Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Create::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.bookings.cancel', \Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Cancel::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.bookings.receipt', \Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Receipt::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.maintenance.index', \Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance\Index::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.maintenance.create', \Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance\Create::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.incidents.index', \Modules\Hostels\Http\Livewire\HostelOccupant\Incidents\Index::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.incidents.create', \Modules\Hostels\Http\Livewire\HostelOccupant\Incidents\Create::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.visitors.index', \Modules\Hostels\Http\Livewire\HostelOccupant\Visitors\Index::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.visitors.create', \Modules\Hostels\Http\Livewire\HostelOccupant\Visitors\Create::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.profile.edit', \Modules\Hostels\Http\Livewire\HostelOccupant\Profile\Edit::class);

        // ── HostelOccupant — new marketplace/entertainment ────────────────────
        \Livewire\Livewire::component('hostels.hostel-occupant.whatsapp-groups.index', \Modules\Hostels\Http\Livewire\HostelOccupant\WhatsAppGroups\Index::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.restaurant.menu', \Modules\Hostels\Http\Livewire\HostelOccupant\Restaurant\Menu::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.shop.index', \Modules\Hostels\Http\Livewire\HostelOccupant\Shop\Index::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.shop.checkout', \Modules\Hostels\Http\Livewire\HostelOccupant\Shop\Checkout::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.shop.orders', \Modules\Hostels\Http\Livewire\HostelOccupant\Shop\Orders::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.movies.index', \Modules\Hostels\Http\Livewire\HostelOccupant\Movies\Index::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.movies.watch', \Modules\Hostels\Http\Livewire\HostelOccupant\Movies\Watch::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.movies.request', \Modules\Hostels\Http\Livewire\HostelOccupant\Movies\Request::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.books.index', \Modules\Hostels\Http\Livewire\HostelOccupant\Books\Index::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.books.checkout', \Modules\Hostels\Http\Livewire\HostelOccupant\Books\Checkout::class);
        \Livewire\Livewire::component('hostels.hostel-occupant.books.orders', \Modules\Hostels\Http\Livewire\HostelOccupant\Books\Orders::class);
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

<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Observers\CompanyObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Factories\Factory::guessFactoryNamesUsing(function (string $modelName) {
            if (str_starts_with($modelName, 'Modules\\')) {
                $parts = explode('\\', $modelName);
                $moduleName = $parts[1];
                $modelClass = $parts[count($parts) - 1];
                return "Modules\\{$moduleName}\\Database\\factories\\{$modelClass}Factory";
            }
            return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });

        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        // Auto-seed roles into new subsidiary companies
        Company::observe(CompanyObserver::class);

        // Configure rate limiting for payment webhooks
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiting for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('payment-attempts', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if (! $user) {
                return null;
            }

            $tableNames = config('permission.table_names');
            $isSuperAdmin = DB::table($tableNames['model_has_roles'])
                ->join($tableNames['roles'], $tableNames['roles'].'.id', '=', $tableNames['model_has_roles'].'.role_id')
                ->where($tableNames['model_has_roles'].'.model_type', get_class($user))
                ->where($tableNames['model_has_roles'].'.model_id', $user->getKey())
                ->where($tableNames['roles'].'.name', 'super_admin')
                ->exists();

            if (! $isSuperAdmin) {
                return null;
            }

            $panelId = null;

            if (class_exists(\Filament\Facades\Filament::class)) {
                try {
                    $panelId = \Filament\Facades\Filament::getCurrentPanel()?->getId();
                } catch (\Throwable $e) {
                    $panelId = null;
                }
            }

            if ($panelId === 'admin' || $panelId === 'company-admin' || $panelId === null) {
                return true;
            }

            return null;
        });

        Gate::define('super_admin', function ($user) {
            return $user && method_exists($user, 'hasRole') && $user->hasRole('super_admin');
        });
    }
}

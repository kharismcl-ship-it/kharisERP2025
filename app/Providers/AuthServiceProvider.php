<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        // Super-admin bypass — checked directly in DB so it works regardless of
        // the current Spatie team context (the global super_admin role has company_id=NULL).
        // Shield also registers a Gate::before for this, but we keep our own as a
        // safety net since Shield's check is team-context-sensitive.
        Gate::before(function ($user, string $ability) {
            if (! $user) {
                return null;
            }

            $tableNames = config('permission.table_names');
            $teamKey    = config('permission.column_names.team_foreign_key', 'team_id');

            $isSuperAdmin = DB::table($tableNames['model_has_roles'])
                ->join($tableNames['roles'], $tableNames['roles'].'.id', '=', $tableNames['model_has_roles'].'.role_id')
                ->where($tableNames['model_has_roles'].'.model_type', get_class($user))
                ->where($tableNames['model_has_roles'].'.model_id', $user->getKey())
                ->where($tableNames['roles'].'.name', 'super_admin')
                ->whereNull($tableNames['roles'].'.'.$teamKey)
                ->exists();

            return $isSuperAdmin ? true : null;
        });

        // Bridge for Shield-generated policy methods: policies call
        // $user->can('Create:Employee') which is a plain Gate ability string
        // (no model). Spatie's register_permission_check_method also does this,
        // but we keep an explicit bridge here for clarity and to ensure
        // PermissionDoesNotExist is always caught cleanly.
        Gate::before(function ($user, string $ability) {
            if (! $user || ! method_exists($user, 'checkPermissionTo')) {
                return null;
            }

            return $user->checkPermissionTo($ability) ?: null;
        });
    }
}

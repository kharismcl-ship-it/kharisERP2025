<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Ensures that any user with a global super_admin role also has the super_admin
 * role recorded for every company tenant (so Shield's tenant-scoped check passes
 * in the company-admin panel).
 *
 * Uses a per-user cache key so the DB writes only happen once per day, not on
 * every single request.
 */
class EnsureGlobalSuperAdminRole
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        // Only do the heavy lifting once per day per user
        $cacheKey = "super_admin_synced_{$user->id}";

        if (Cache::has($cacheKey)) {
            return $next($request);
        }

        $this->syncSuperAdminRoles($user);

        // Cache for 24 hours — invalidated if new companies are created
        Cache::put($cacheKey, true, now()->addHours(24));

        return $next($request);
    }

    private function syncSuperAdminRoles($user): void
    {
        $tableNames = config('permission.table_names');
        $teamKey    = config('permission.column_names.team_foreign_key', 'team_id');

        // Is this user a GLOBAL super_admin?
        // Check that the user holds the global super_admin ROLE (role.company_id IS NULL).
        // We cannot check model_has_roles.company_id IS NULL because that column is NOT NULL
        // in this schema (it was added as part of the PRIMARY KEY by the original migration).
        $isGlobalSuperAdmin = DB::table($tableNames['model_has_roles'])
            ->join($tableNames['roles'], $tableNames['roles'].'.id', '=', $tableNames['model_has_roles'].'.role_id')
            ->where($tableNames['model_has_roles'].'.model_type', get_class($user))
            ->where($tableNames['model_has_roles'].'.model_id', $user->getKey())
            ->where($tableNames['roles'].'.name', 'super_admin')
            ->whereNull($tableNames['roles'].'.'.$teamKey)
            ->exists();

        if (! $isGlobalSuperAdmin) {
            return;
        }

        // Ensure a global (unscoped) super_admin role row exists in roles table
        $globalRoleId = DB::table($tableNames['roles'])
            ->where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->whereNull($teamKey)
            ->value('id');

        if (! $globalRoleId) {
            $globalRoleId = DB::table($tableNames['roles'])->insertGetId([
                'name'       => 'super_admin',
                'guard_name' => 'web',
                $teamKey     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // For each existing company, ensure a company-scoped super_admin role exists
        // and that this user has it — so Shield's scopeToTenant check passes.
        $companyIds = DB::table('companies')->pluck('id');

        foreach ($companyIds as $companyId) {
            // Ensure a company-scoped super_admin role exists
            $companyRoleId = DB::table($tableNames['roles'])
                ->where('name', 'super_admin')
                ->where('guard_name', 'web')
                ->where($teamKey, $companyId)
                ->value('id');

            if (! $companyRoleId) {
                $companyRoleId = DB::table($tableNames['roles'])->insertGetId([
                    'name'       => 'super_admin',
                    'guard_name' => 'web',
                    $teamKey     => $companyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Assign the company-scoped super_admin role to this user.
            // Use insertOrIgnore (atomic INSERT IGNORE) instead of updateOrInsert to avoid a
            // race condition: if the cache is cleared, multiple simultaneous Livewire requests
            // can all pass the Cache::has() check before the cache is written, causing all of
            // them to attempt the same INSERT and hitting a duplicate-key violation.
            DB::table($tableNames['model_has_roles'])->insertOrIgnore([
                'role_id'    => $companyRoleId,
                'model_id'   => $user->getKey(),
                'model_type' => get_class($user),
                $teamKey     => $companyId,
            ]);
        }

        // NOTE: We intentionally skip a NULL company_id entry here because the
        // model_has_roles.company_id column is NOT NULL (part of the PRIMARY KEY).
        // The global super_admin role (role.company_id=NULL) is detected via the
        // isGlobalSuperAdmin() check above which inspects roles.company_id instead.
    }
}

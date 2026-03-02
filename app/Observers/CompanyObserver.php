<?php

namespace App\Observers;

use App\Jobs\SeedSubsidiaryRolesJob;
use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Observes Company model events to keep role scaffolding in sync.
 *
 * When a company is saved as a subsidiary (type = 'subsidiary') —
 * either newly created or converted from another type — this observer:
 *
 *  1. Dispatches SeedSubsidiaryRolesJob (after DB commit) to clone the
 *     parent company's roles into the new subsidiary.
 *
 *  2. Clears the cached super_admin sync flag for every global super_admin
 *     so that EnsureGlobalSuperAdminRole will propagate the global
 *     super_admin role to the new company on the very next HTTP request.
 */
class CompanyObserver
{
    public function saved(Company $company): void
    {
        $isSubsidiary = $company->type === 'subsidiary';
        $isNew        = $company->wasRecentlyCreated;
        $typeChanged  = $company->wasChanged('type');

        // Only act when a subsidiary is first created or a company is converted
        if (! $isSubsidiary || (! $isNew && ! $typeChanged)) {
            return;
        }

        // Dispatch role seeding after the outer DB transaction has committed
        // so the subsidiary record is fully visible to the queue worker.
        SeedSubsidiaryRolesJob::dispatch($company->id)->afterCommit();

        // Force EnsureGlobalSuperAdminRole to run again on the next request
        // for every global super_admin so the new company gets the role immediately.
        $this->invalidateSuperAdminCaches();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Clear the 24-hour super_admin sync cache for every user that holds
     * a global (unscoped) super_admin role.
     */
    private function invalidateSuperAdminCaches(): void
    {
        $teamKey    = config('permission.column_names.team_foreign_key', 'company_id');
        $tableNames = config('permission.table_names');

        $userIds = DB::table($tableNames['model_has_roles'])
            ->join(
                $tableNames['roles'],
                $tableNames['roles'] . '.id',
                '=',
                $tableNames['model_has_roles'] . '.role_id'
            )
            ->where($tableNames['roles'] . '.name', 'super_admin')
            ->whereNull($tableNames['model_has_roles'] . '.' . $teamKey)
            ->pluck($tableNames['model_has_roles'] . '.model_id');

        foreach ($userIds as $userId) {
            Cache::forget("super_admin_synced_{$userId}");
        }
    }
}
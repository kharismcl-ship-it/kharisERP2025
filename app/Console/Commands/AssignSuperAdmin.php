<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignSuperAdmin extends Command
{
    protected $signature = 'shield:super-admin-gate
                            {--user-id= : ID of the user}
                            {--email=   : Email of the user}';

    protected $description = 'Assign a global (unscoped) super_admin role to a user so they can access all panels and all companies';

    public function handle(): void
    {
        $user = $this->resolveUser();
        if (! $user) {
            return;
        }

        $teamKey    = config('permission.column_names.team_foreign_key', 'team_id');
        $tableNames = config('permission.table_names');

        // 1. Ensure a global (team_id = NULL) super_admin role exists in the roles table
        //    We must bypass Spatie's team scope by querying the DB directly.
        $globalRoleId = DB::table($tableNames['roles'])
            ->where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->whereNull($teamKey)
            ->value('id');

        if (! $globalRoleId) {
            $globalRoleId = DB::table($tableNames['roles'])->insertGetId([
                'name'       => 'super_admin',
                'guard_name' => 'web',
                $teamKey     => null,   // NULL = global, no company scope
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->info("Created global super_admin role (id={$globalRoleId}).");
        } else {
            $this->info("Global super_admin role already exists (id={$globalRoleId}).");
        }

        // 2. Assign the global role to the user for every company.
        //    model_has_roles.company_id is NOT NULL (part of the PRIMARY KEY), so we cannot
        //    insert with NULL. The "global" nature is detected by checking roles.company_id IS NULL
        //    (the role's own column), not the pivot column. EnsureGlobalSuperAdminRole will keep
        //    new companies in sync automatically on the user's next login.
        $companyIds = DB::table('companies')->pluck('id');

        if ($companyIds->isEmpty()) {
            $this->error('No companies found. Create at least one company first, then re-run this command.');
            return;
        }

        foreach ($companyIds as $companyId) {
            DB::table($tableNames['model_has_roles'])->insertOrIgnore([
                'role_id'    => $globalRoleId,
                'model_id'   => $user->id,
                'model_type' => get_class($user),
                $teamKey     => $companyId,
            ]);
        }

        // Invalidate the per-user sync cache so EnsureGlobalSuperAdminRole re-runs on next request
        \Illuminate\Support\Facades\Cache::forget("super_admin_synced_{$user->id}");

        // 3. Verify
        $ok = DB::table($tableNames['model_has_roles'])
            ->join($tableNames['roles'], $tableNames['roles'] . '.id', '=', $tableNames['model_has_roles'] . '.role_id')
            ->where($tableNames['model_has_roles'] . '.model_id', $user->id)
            ->where($tableNames['model_has_roles'] . '.model_type', get_class($user))
            ->where($tableNames['roles'] . '.name', 'super_admin')
            ->whereNull($tableNames['roles'] . '.' . $teamKey)
            ->exists();

        if ($ok) {
            $this->info("✓ {$user->email} is now a global super_admin.");
            $this->info('  They can access the admin panel and all company-admin tenants.');
            $this->info('  The EnsureGlobalSuperAdminRole middleware will propagate');
            $this->info('  per-company super_admin assignments automatically on first login.');
        } else {
            $this->error('❌ Role assignment failed — check DB constraints.');
        }
    }

    private function resolveUser(): ?User
    {
        $userId = $this->option('user-id');
        $email  = $this->option('email');

        if (! $userId && ! $email) {
            $this->error('Provide --user-id or --email.');
            return null;
        }

        $user = $userId
            ? User::find($userId)
            : User::where('email', $email)->first();

        if (! $user) {
            $this->error('User not found.');
            return null;
        }

        return $user;
    }
}

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

        // 2. Assign the global role to the user (team_id = NULL)
        DB::table($tableNames['model_has_roles'])->updateOrInsert(
            [
                'role_id'    => $globalRoleId,
                'model_id'   => $user->id,
                'model_type' => get_class($user),
                $teamKey     => null,
            ],
            [
                'role_id'    => $globalRoleId,
                'model_id'   => $user->id,
                'model_type' => get_class($user),
                $teamKey     => null,
            ]
        );

        // 3. Verify
        $ok = DB::table($tableNames['model_has_roles'])
            ->where('role_id', $globalRoleId)
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->whereNull($teamKey)
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

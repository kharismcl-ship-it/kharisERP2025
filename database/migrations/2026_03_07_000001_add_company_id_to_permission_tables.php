<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Retrofits the Spatie Permission tables for team-based (company) scoping.
 *
 * The original `create_permission_tables` migration ran with `teams = false`,
 * so the `company_id` team_foreign_key column was never added.  With
 * `teams = true` and `team_foreign_key = 'company_id'` in config/permission.php,
 * Spatie now queries `WHERE model_has_roles.company_id IS NULL` — which fails
 * because the column does not exist.
 *
 * This migration is idempotent: each change is guarded by a `hasColumn` check.
 */
return new class extends Migration
{
    public function up(): void
    {
        $columnNames = config('permission.column_names');
        $tableNames  = config('permission.table_names');

        $teamKey   = $columnNames['team_foreign_key']    ?? 'company_id';
        $pivotRole = $columnNames['role_pivot_key']      ?? 'role_id';
        $pivotPerm = $columnNames['permission_pivot_key'] ?? 'permission_id';
        $morphKey  = $columnNames['model_morph_key']     ?? 'model_id';

        // ── 1. roles ─────────────────────────────────────────────────────────
        if (! Schema::hasColumn($tableNames['roles'], $teamKey)) {
            Schema::table($tableNames['roles'], function (Blueprint $table) use ($teamKey) {
                $table->unsignedBigInteger($teamKey)->nullable()->after('id');
                $table->index($teamKey, 'roles_team_foreign_key_index');
            });

            // Swap the unique index from (name, guard_name) → (company_id, name, guard_name)
            try {
                DB::statement("ALTER TABLE `{$tableNames['roles']}` DROP INDEX `roles_name_guard_name_unique`");
            } catch (\Throwable) {
                // Already dropped or named differently — safe to continue.
            }

            DB::statement(
                "ALTER TABLE `{$tableNames['roles']}`
                 ADD UNIQUE KEY `roles_team_name_guard_unique` (`{$teamKey}`, `name`, `guard_name`)"
            );
        }

        // ── 2. model_has_roles ───────────────────────────────────────────────
        if (! Schema::hasColumn($tableNames['model_has_roles'], $teamKey)) {
            // MySQL error 1553: cannot drop PRIMARY KEY while a FK constraint uses
            // it as the only index covering the FK column (role_id).
            // Solution: drop the FK first, drop + recreate the PK, restore the FK.
            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($pivotRole) {
                $table->dropForeign([$pivotRole]);
            });

            DB::statement("ALTER TABLE `{$tableNames['model_has_roles']}` DROP PRIMARY KEY");

            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($teamKey) {
                // Nullable so global (non-company-scoped) assignments store NULL.
                $table->unsignedBigInteger($teamKey)->nullable()->after('model_id');
                $table->index($teamKey, 'model_has_roles_team_foreign_key_index');
            });

            // Restore the original PK shape (without company_id — MySQL disallows
            // NULL in a PK, and company_id is nullable). The unique index below
            // handles the team-scoped uniqueness constraint instead.
            DB::statement(
                "ALTER TABLE `{$tableNames['model_has_roles']}`
                 ADD PRIMARY KEY (`{$pivotRole}`, `{$morphKey}`, `model_type`)"
            );

            // Team-scoped unique index. NULL company_id = global assignment.
            DB::statement(
                "CREATE UNIQUE INDEX `model_has_roles_team_role_model_unique`
                 ON `{$tableNames['model_has_roles']}` (`{$teamKey}`, `{$pivotRole}`, `{$morphKey}`, `model_type`)"
            );

            // Restore the FK constraint that was dropped above.
            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $pivotRole) {
                $table->foreign($pivotRole)
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');
            });
        }

        // ── 3. model_has_permissions ─────────────────────────────────────────
        if (! Schema::hasColumn($tableNames['model_has_permissions'], $teamKey)) {
            // Same FK-before-PK pattern as model_has_roles above.
            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($pivotPerm) {
                $table->dropForeign([$pivotPerm]);
            });

            DB::statement("ALTER TABLE `{$tableNames['model_has_permissions']}` DROP PRIMARY KEY");

            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($teamKey) {
                $table->unsignedBigInteger($teamKey)->nullable()->after('model_id');
                $table->index($teamKey, 'model_has_permissions_team_foreign_key_index');
            });

            DB::statement(
                "ALTER TABLE `{$tableNames['model_has_permissions']}`
                 ADD PRIMARY KEY (`{$pivotPerm}`, `{$morphKey}`, `model_type`)"
            );

            DB::statement(
                "CREATE UNIQUE INDEX `model_has_permissions_team_perm_model_unique`
                 ON `{$tableNames['model_has_permissions']}` (`{$teamKey}`, `{$pivotPerm}`, `{$morphKey}`, `model_type`)"
            );

            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $pivotPerm) {
                $table->foreign($pivotPerm)
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');
            });
        }

        // ── 4. Clear the Spatie permission cache ────────────────────────────
        app('cache')
            ->store(config('permission.cache.store') !== 'default'
                ? config('permission.cache.store')
                : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $columnNames = config('permission.column_names');
        $tableNames  = config('permission.table_names');
        $teamKey     = $columnNames['team_foreign_key'] ?? 'company_id';

        // Drop the team-scoped unique indexes before dropping the column.
        if (Schema::hasColumn($tableNames['model_has_roles'], $teamKey)) {
            try {
                DB::statement("DROP INDEX `model_has_roles_team_role_model_unique` ON `{$tableNames['model_has_roles']}`");
            } catch (\Throwable) {}

            Schema::table($tableNames['model_has_roles'], fn (Blueprint $t) => $t->dropColumn($teamKey));
        }

        if (Schema::hasColumn($tableNames['model_has_permissions'], $teamKey)) {
            try {
                DB::statement("DROP INDEX `model_has_permissions_team_perm_model_unique` ON `{$tableNames['model_has_permissions']}`");
            } catch (\Throwable) {}

            Schema::table($tableNames['model_has_permissions'], fn (Blueprint $t) => $t->dropColumn($teamKey));
        }

        if (Schema::hasColumn($tableNames['roles'], $teamKey)) {
            Schema::table($tableNames['roles'], fn (Blueprint $t) => $t->dropColumn($teamKey));
        }
    }
};
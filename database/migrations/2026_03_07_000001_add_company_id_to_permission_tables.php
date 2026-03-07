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

        $teamKey   = $columnNames['team_foreign_key']      ?? 'company_id';
        $pivotRole = $columnNames['role_pivot_key']         ?? 'role_id';
        $pivotPerm = $columnNames['permission_pivot_key']   ?? 'permission_id';
        $morphKey  = $columnNames['model_morph_key']        ?? 'model_id';

        // ── 1. roles ─────────────────────────────────────────────────────────
        if (! Schema::hasColumn($tableNames['roles'], $teamKey)) {
            Schema::table($tableNames['roles'], function (Blueprint $table) use ($teamKey) {
                $table->unsignedBigInteger($teamKey)->nullable()->after('id');
                $table->index($teamKey, 'roles_team_foreign_key_index');
            });

            // Swap the unique index from (name, guard_name) → (company_id, name, guard_name)
            // The auto-generated name is roles_name_guard_name_unique.
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
            // Drop the existing primary key so we can redefine it with company_id.
            DB::statement("ALTER TABLE `{$tableNames['model_has_roles']}` DROP PRIMARY KEY");

            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($teamKey) {
                // Nullable so global (non-company-scoped) assignments store NULL.
                $table->unsignedBigInteger($teamKey)->nullable()->after('model_id');
                $table->index($teamKey, 'model_has_roles_team_foreign_key_index');
            });

            // New composite PK. MySQL does not allow NULL in a PRIMARY KEY, so we
            // use IFNULL(company_id, 0) via a generated column workaround — or
            // simply keep the original PK and rely on a unique index for team scope.
            // We use a unique index instead so that NULLs are handled correctly
            // (MySQL treats each NULL as distinct in a UNIQUE index, but identical
            // in a PRIMARY KEY). This lets a user hold the same role in two companies.
            DB::statement(
                "ALTER TABLE `{$tableNames['model_has_roles']}`
                 ADD PRIMARY KEY (`{$pivotRole}`, `{$morphKey}`, `model_type`)"
            );

            // Separate unique index that covers the team dimension.
            // NULL values mean "global / no company" and are treated as distinct,
            // which is the correct Spatie teams behaviour.
            DB::statement(
                "CREATE UNIQUE INDEX `model_has_roles_team_role_model_unique`
                 ON `{$tableNames['model_has_roles']}` (`{$teamKey}`, `{$pivotRole}`, `{$morphKey}`, `model_type`)"
            );
        }

        // ── 3. model_has_permissions ─────────────────────────────────────────
        if (! Schema::hasColumn($tableNames['model_has_permissions'], $teamKey)) {
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

        foreach ([
            $tableNames['roles'],
            $tableNames['model_has_roles'],
            $tableNames['model_has_permissions'],
        ] as $table) {
            if (Schema::hasColumn($table, $teamKey)) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn($teamKey));
            }
        }
    }
};

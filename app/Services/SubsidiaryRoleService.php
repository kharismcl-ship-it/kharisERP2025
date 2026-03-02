<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Clones roles (and their permission assignments) from a parent/HQ company
 * into a subsidiary company so the subsidiary starts with a full permission set.
 *
 * - `super_admin` is always skipped — it is handled automatically by
 *   EnsureGlobalSuperAdminRole middleware on the next request.
 * - Already-existing roles on the subsidiary are left untouched (idempotent).
 * - If the subsidiary has no parent_company_id, the method falls back to the
 *   first HQ company (type = 'hq') in the database.
 */
class SubsidiaryRoleService
{
    /** @var string */
    private string $teamKey;

    /** @var array<string, string> */
    private array $tableNames;

    public function __construct()
    {
        $this->teamKey    = config('permission.column_names.team_foreign_key', 'company_id');
        $this->tableNames = config('permission.table_names');
    }

    /**
     * Seed roles for a single subsidiary.
     *
     * @return array{
     *   company_id:     int,
     *   company_name:   string,
     *   source_company: string,
     *   roles_created:  string[],
     *   roles_skipped:  string[],
     * }
     */
    public function seedForCompany(Company $subsidiary): array
    {
        $parentId = $subsidiary->parent_company_id ?? $this->resolveHqId();

        if (! $parentId) {
            Log::warning("SubsidiaryRoleService: no parent/HQ found for company {$subsidiary->id} — skipping.");
            return [
                'company_id'     => $subsidiary->id,
                'company_name'   => $subsidiary->name,
                'source_company' => 'none',
                'roles_created'  => [],
                'roles_skipped'  => [],
            ];
        }

        $sourceCompany = Company::find($parentId);

        // Load all non-super_admin roles from the source company with their permissions
        $sourceRoles = Role::with('permissions')
            ->where($this->teamKey, $parentId)
            ->where('name', '!=', 'super_admin')
            ->get();

        $created = [];
        $skipped = [];

        foreach ($sourceRoles as $sourceRole) {
            $existing = Role::where('name', $sourceRole->name)
                ->where('guard_name', $sourceRole->guard_name)
                ->where($this->teamKey, $subsidiary->id)
                ->first();

            if ($existing) {
                $skipped[] = $sourceRole->name;
                continue;
            }

            // Create the role scoped to the subsidiary
            $newRole = Role::create([
                'name'          => $sourceRole->name,
                'guard_name'    => $sourceRole->guard_name,
                $this->teamKey  => $subsidiary->id,
            ]);

            // Copy every permission from the source role
            $newRole->syncPermissions($sourceRole->permissions);

            $created[] = $sourceRole->name;
        }

        // Seed the company-scoped super_admin role immediately so the
        // EnsureGlobalSuperAdminRole middleware can pick it up on the very next
        // request without waiting.
        $this->ensureSuperAdminRole($subsidiary);

        Log::info("SubsidiaryRoleService: seeded company {$subsidiary->id} ({$subsidiary->name})", [
            'source'  => $sourceCompany?->name ?? $parentId,
            'created' => $created,
            'skipped' => $skipped,
        ]);

        return [
            'company_id'     => $subsidiary->id,
            'company_name'   => $subsidiary->name,
            'source_company' => $sourceCompany?->name ?? (string) $parentId,
            'roles_created'  => $created,
            'roles_skipped'  => $skipped,
        ];
    }

    /**
     * Seed roles for every subsidiary that descends from a given company.
     * Useful for bulk backfilling.
     *
     * @return array<int, array> Keyed by company ID
     */
    public function seedForAllSubsidiaries(?int $parentId = null): array
    {
        $query = Company::where('type', 'subsidiary');

        if ($parentId !== null) {
            $query->where('parent_company_id', $parentId);
        }

        $results = [];

        foreach ($query->get() as $subsidiary) {
            $results[$subsidiary->id] = $this->seedForCompany($subsidiary);
        }

        return $results;
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    /**
     * Ensure the company-scoped super_admin role row exists in the roles table.
     * The actual user assignment is handled by EnsureGlobalSuperAdminRole.
     */
    private function ensureSuperAdminRole(Company $company): void
    {
        DB::table($this->tableNames['roles'])->updateOrInsert(
            [
                'name'          => 'super_admin',
                'guard_name'    => 'web',
                $this->teamKey  => $company->id,
            ],
            [
                'name'          => 'super_admin',
                'guard_name'    => 'web',
                $this->teamKey  => $company->id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        );
    }

    /**
     * Returns the ID of the first HQ company, used as fallback source.
     */
    private function resolveHqId(): ?int
    {
        return Company::where('type', 'hq')->value('id');
    }
}
<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\SubsidiaryRoleService;
use Illuminate\Console\Command;

/**
 * Backfills default roles into subsidiary companies by cloning them from
 * the parent (or HQ) company.
 *
 * Usage:
 *   php artisan companies:seed-roles            # all subsidiaries
 *   php artisan companies:seed-roles 5          # single company by ID
 *   php artisan companies:seed-roles --parent=1 # all subsidiaries of company 1
 */
class SeedSubsidiaryRolesCommand extends Command
{
    protected $signature = 'companies:seed-roles
                            {company? : Company ID to seed (omit for all subsidiaries)}
                            {--parent= : Only seed subsidiaries of this parent company ID}';

    protected $description = 'Seed default roles into subsidiary companies by cloning from their parent';

    public function handle(SubsidiaryRoleService $service): int
    {
        $companyId = $this->argument('company');
        $parentId  = $this->option('parent');

        if ($companyId !== null) {
            return $this->seedSingle((int) $companyId, $service);
        }

        return $this->seedAll($parentId !== null ? (int) $parentId : null, $service);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function seedSingle(int $companyId, SubsidiaryRoleService $service): int
    {
        $company = Company::find($companyId);

        if (! $company) {
            $this->error("Company {$companyId} not found.");
            return self::FAILURE;
        }

        $this->info("Seeding roles for: {$company->name} (ID {$company->id})...");

        $result = $service->seedForCompany($company);

        $this->renderResult($result);

        return self::SUCCESS;
    }

    private function seedAll(?int $parentId, SubsidiaryRoleService $service): int
    {
        $query = Company::where('type', 'subsidiary');

        if ($parentId !== null) {
            $query->where('parent_company_id', $parentId);
            $this->info("Seeding roles for all subsidiaries of company ID {$parentId}...");
        } else {
            $this->info('Seeding roles for ALL subsidiary companies...');
        }

        $subsidiaries = $query->get();

        if ($subsidiaries->isEmpty()) {
            $this->warn('No subsidiary companies found.');
            return self::SUCCESS;
        }

        $this->withProgressBar($subsidiaries, function (Company $subsidiary) use ($service) {
            $service->seedForCompany($subsidiary);
        });

        $this->newLine(2);

        // Print a summary table
        $rows = $subsidiaries->map(function (Company $company) use ($service) {
            $result = $service->seedForCompany($company);
            return [
                $result['company_id'],
                $result['company_name'],
                $result['source_company'],
                implode(', ', $result['roles_created']) ?: '—',
                implode(', ', $result['roles_skipped']) ?: '—',
            ];
        });

        $this->table(
            ['ID', 'Company', 'Source', 'Roles Created', 'Roles Skipped'],
            $rows->all()
        );

        return self::SUCCESS;
    }

    private function renderResult(array $result): void
    {
        $this->table(
            ['Field', 'Value'],
            [
                ['Company ID',     $result['company_id']],
                ['Company Name',   $result['company_name']],
                ['Source Company', $result['source_company']],
                ['Roles Created',  implode(', ', $result['roles_created']) ?: '(none)'],
                ['Roles Skipped',  implode(', ', $result['roles_skipped']) ?: '(none)'],
            ]
        );
    }
}
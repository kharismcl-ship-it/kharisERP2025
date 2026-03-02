<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\SubsidiaryRoleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queued job that seeds default roles into a newly created subsidiary company.
 *
 * Dispatched by CompanyObserver after the DB transaction commits so the
 * subsidiary record is fully visible to the queue worker.
 */
class SeedSubsidiaryRolesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Retry up to 3 times on failure (e.g. DB lock during migration). */
    public int $tries = 3;

    /** Wait 5 seconds between retries. */
    public int $backoff = 5;

    public function __construct(public readonly int $companyId) {}

    public function handle(SubsidiaryRoleService $service): void
    {
        $company = Company::find($this->companyId);

        if (! $company) {
            Log::warning("SeedSubsidiaryRolesJob: company {$this->companyId} not found — skipped.");
            return;
        }

        $result = $service->seedForCompany($company);

        Log::info('SeedSubsidiaryRolesJob: completed', $result);
    }
}
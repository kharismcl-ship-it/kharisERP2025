<?php

namespace App\Http\Responses;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

/**
 * Smart post-login redirect.
 *
 * Routing logic:
 *  1. Global super_admin  → /admin
 *  2. Management role     → /company-admin/{company-slug}
 *  3. Regular employee    → /staff/{company-slug}
 *  4. No company assigned → /dashboard (fallback)
 *
 * "Management role" = user has at least one role whose name contains
 * "admin" or "manager" (case-insensitive), which is the convention
 * for roles set up via Shield in the company-admin panel.
 */
class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        // 1. Super admin → global admin panel
        if ($user->isGlobalSuperAdmin()) {
            return redirect('/admin');
        }

        // Get first active company to build the tenant URL
        $company = $user->activeCompanies()->first();

        if (! $company) {
            return redirect('/dashboard');
        }

        $companySlug = $company->slug ?? $company->id;

        // 2. Management roles → company-admin panel
        if ($this->isManagementUser($user)) {
            return redirect("/company-admin/{$companySlug}");
        }

        // 3. All other employees → staff self-service panel
        return redirect("/staff/{$companySlug}");
    }

    /**
     * Determine if a user should be routed to the management panel.
     *
     * A user is considered a management user if they hold ANY role whose
     * name contains "admin" or "manager" (excluding the global super_admin
     * which is already handled above).
     */
    private function isManagementUser(User $user): bool
    {
        return $user->roles()
            ->where(function ($q) {
                $q->where('name', 'like', '%admin%')
                  ->orWhere('name', 'like', '%manager%');
            })
            ->exists();
    }
}

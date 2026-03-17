<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;

/**
 * Sets the Spatie permission "team" context based on the active Filament panel.
 *
 * - admin panel        → setPermissionsTeamId(null)  → global roles (company_id IS NULL)
 * - company-admin      → setPermissionsTeamId($id)   → company-scoped roles
 *
 * Registered both as regular panel middleware AND as a Livewire persistent
 * middleware (via CompanyAdminPanelProvider::persistentMiddleware). This ensures
 * the team context is set for both full page requests AND Livewire AJAX requests
 * (POST /livewire/update). For Livewire requests Filament's IdentifyTenant
 * persistent middleware runs first, so Filament::getTenant() is already resolved
 * and we use that as the primary source.
 */
class SyncSpatiePermissionsWithFilamentTenants
{
    public function handle($request, Closure $next)
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        $tenantedPanels = ['company-admin', 'staff'];

        if ($panel && in_array($panel->getId(), $tenantedPanels)) {
            $tenantId = $this->resolveTenantId($request);

            if ($tenantId) {
                setPermissionsTeamId($tenantId);
            } else {
                // Tenant-switcher / login page — no tenant in URL yet
                setPermissionsTeamId(null);
            }
        } else {
            // Admin panel — use global (unscoped) permissions
            setPermissionsTeamId(null);
        }

        return $next($request);
    }

    private function resolveTenantId($request): ?int
    {
        // Primary: Filament::getTenant() — works for both full requests
        // (resolved by IdentifyTenant middleware) and Livewire AJAX requests
        // (resolved by Filament's IdentifyTenant persistent middleware which
        // runs before this middleware in the persistent middleware chain).
        $tenant = Filament::getTenant();
        if ($tenant) {
            return (int) $tenant->getKey();
        }

        // Fallback: route parameter (used during initial page requests before
        // Filament has set the tenant, or in edge cases where IdentifyTenant
        // did not run).
        $routeTenant = $request->route('tenant');
        if ($routeTenant) {
            return is_object($routeTenant)
                ? (int) $routeTenant->getKey()
                : (int) $routeTenant;
        }

        return null;
    }
}

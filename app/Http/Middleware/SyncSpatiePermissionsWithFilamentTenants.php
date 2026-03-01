<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;

/**
 * Sets the Spatie permission "team" context based on the active Filament panel.
 *
 * - admin panel        → setPermissionsTeamId(null)  → global roles (company_id IS NULL)
 * - company-admin      → setPermissionsTeamId($id)   → company-scoped roles
 */
class SyncSpatiePermissionsWithFilamentTenants
{
    public function handle($request, Closure $next)
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        if ($panel && $panel->getId() === 'company-admin') {
            $tenant = Filament::getTenant();

            if ($tenant) {
                // Scope all permission lookups to this company
                setPermissionsTeamId($tenant->getKey());
            } else {
                // Tenant not resolved yet (e.g. tenant-switcher page) — clear scope
                setPermissionsTeamId(null);
            }
        } else {
            // Admin panel or any other panel — use global (unscoped) permissions
            setPermissionsTeamId(null);
        }

        return $next($request);
    }
}

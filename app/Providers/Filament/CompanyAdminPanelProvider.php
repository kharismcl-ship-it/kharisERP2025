<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureGlobalSuperAdminRole;
use App\Http\Middleware\SyncSpatiePermissionsWithFilamentTenants;
use App\Models\Company;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Construction\Filament\ConstructionFilamentPlugin;
use Modules\Core\Filament\CoreFilamentPlugin;
use Modules\Farms\Filament\FarmsFilamentPlugin;
use Modules\Finance\Filament\FinanceFilamentPlugin;
use Modules\Fleet\Filament\FleetFilamentPlugin;
use Modules\HR\Filament\HRFilamentPlugin;
use Modules\Hostels\Filament\HostelsFilamentPlugin;
use Modules\ManufacturingPaper\Filament\ManufacturingPaperFilamentPlugin;
use Modules\ManufacturingWater\Filament\ManufacturingWaterFilamentPlugin;
use Modules\ProcurementInventory\Filament\ProcurementInventoryFilamentPlugin;
use Modules\Sales\Filament\SalesFilamentPlugin;
use Modules\Requisition\Filament\RequisitionFilamentPlugin;
use Modules\ClientService\Filament\ClientServiceFilamentPlugin;
use Modules\ITSupport\Filament\ITSupportFilamentPlugin;

class CompanyAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('company-admin')
            ->path('company-admin')
            ->login()
            ->brandName('Kharis ERP')
            ->databaseNotifications()
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->colors([
                'primary' => Color::Amber,
            ])

            // ── Filament Multi-Tenancy ────────────────────────────────────────
            // Tenant = Company. Filament stores the selected company in the session
            // and makes it available via Filament::getTenant() on every request.
            //
            // Group / HQ companies (type = 'hq') automatically see data from all
            // their subsidiaries via the TenantScope global scope attached to every
            // model through the BelongsToCompany trait.  No extra configuration is
            // needed here — the scope expands company_id lookups to
            // selfAndDescendantIds() whenever an HQ tenant is active.
            ->tenant(Company::class)
            ->tenantMenuItems([
                // Show a badge on the tenant switcher so the user knows when they
                // are operating in group (multi-subsidiary) mode.
                \Filament\Navigation\MenuItem::make()
                    ->label(fn () => \App\Models\Company::find(
                        optional(\Filament\Facades\Filament::getTenant())->getKey()
                    )?->isGroupCompany()
                        ? 'Group view (all subsidiaries visible)'
                        : 'Company view'
                    )
                    ->icon(fn () => \App\Models\Company::find(
                        optional(\Filament\Facades\Filament::getTenant())->getKey()
                    )?->isGroupCompany()
                        ? 'heroicon-o-building-office-2'
                        : 'heroicon-o-building-office'
                    )
                    ->url('#')
                    ->visible(true),
            ])

            // ── Resources & Pages ─────────────────────────────────────────────
            ->discoverResources(in: app_path('Filament/CompanyAdmin/Resources'), for: 'App\Filament\CompanyAdmin\Resources')
            ->discoverPages(in: app_path('Filament/CompanyAdmin/Pages'), for: 'App\Filament\CompanyAdmin\Pages')
            ->discoverWidgets(in: app_path('Filament/CompanyAdmin/Widgets'), for: 'App\Filament\CompanyAdmin\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
            ])

            // ── Middleware ────────────────────────────────────────────────────
            // Use the same Laravel middleware stack as AdminPanelProvider for
            // consistent session/CSRF handling. The SyncSpatie middleware is
            // critical: it calls setPermissionsTeamId($company->id) AFTER
            // Filament has resolved the tenant.
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                // IMPORTANT: runs AFTER tenant is resolved by Filament's routing
                SyncSpatiePermissionsWithFilamentTenants::class,
                EnsureGlobalSuperAdminRole::class,
            ])

            // ── Filament Shield ───────────────────────────────────────────────
            // scopeToTenant(true)                → Shield reads/writes roles only
            //                                      for the current company_id.
            // tenantOwnershipRelationshipName    → matches the `team()` BelongsTo
            //                                      on App\Models\Role (→ Company).
            ->plugins([
                FilamentShieldPlugin::make()
                    ->scopeToTenant(true)
                    ->tenantOwnershipRelationshipName('team'),
                HRFilamentPlugin::make(),
                FinanceFilamentPlugin::make(),
                HostelsFilamentPlugin::make(),
                CoreFilamentPlugin::make(),
                ProcurementInventoryFilamentPlugin::make(),
                FleetFilamentPlugin::make(),
                ConstructionFilamentPlugin::make(),
                FarmsFilamentPlugin::make(),
                ManufacturingPaperFilamentPlugin::make(),
                ManufacturingWaterFilamentPlugin::make(),
                SalesFilamentPlugin::make(),
                RequisitionFilamentPlugin::make(),
                ClientServiceFilamentPlugin::make(),
                ITSupportFilamentPlugin::make(),
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
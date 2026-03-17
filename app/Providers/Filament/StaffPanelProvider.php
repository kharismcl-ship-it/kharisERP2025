<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureGlobalSuperAdminRole;
use App\Http\Middleware\SyncSpatiePermissionsWithFilamentTenants;
use App\Models\Company;
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
use Modules\ClientService\Filament\ClientServiceStaffPlugin;
use Modules\Construction\Filament\ConstructionStaffPlugin;
use Modules\Farms\Filament\FarmsStaffPlugin;
use Modules\Finance\Filament\FinanceStaffPlugin;
use Modules\Hostels\Filament\HostelsStaffPlugin;
use Modules\Fleet\Filament\FleetStaffPlugin;
use Modules\HR\Filament\HRStaffPlugin;
use Modules\ITSupport\Filament\ITSupportStaffPlugin;
use Modules\ManufacturingPaper\Filament\ManufacturingPaperStaffPlugin;
use Modules\ManufacturingWater\Filament\ManufacturingWaterStaffPlugin;
use Modules\Requisition\Filament\RequisitionStaffPlugin;
use Modules\Sales\Filament\SalesStaffPlugin;

class StaffPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('staff')
            ->path('staff')
            ->login()
            ->brandName('Kharis ERP — Staff Portal')
            ->databaseNotifications()
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->colors([
                'primary' => Color::Teal,
            ])

            // Same multi-tenant setup as company-admin — employees select their company
            ->tenant(Company::class)

            ->discoverResources(in: app_path('Filament/Staff/Resources'), for: 'App\Filament\Staff\Resources')
            ->discoverPages(in: app_path('Filament/Staff/Pages'), for: 'App\Filament\Staff\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
            ])

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
                SyncSpatiePermissionsWithFilamentTenants::class,
                EnsureGlobalSuperAdminRole::class,
            ])
            ->persistentMiddleware([
                SyncSpatiePermissionsWithFilamentTenants::class,
                EnsureGlobalSuperAdminRole::class,
            ])

            ->plugins([
                HRStaffPlugin::make(),
                RequisitionStaffPlugin::make(),
                ITSupportStaffPlugin::make(),
                ConstructionStaffPlugin::make(),
                FleetStaffPlugin::make(),
                SalesStaffPlugin::make(),
                ManufacturingPaperStaffPlugin::make(),
                ManufacturingWaterStaffPlugin::make(),
                ClientServiceStaffPlugin::make(),
                FinanceStaffPlugin::make(),
                FarmsStaffPlugin::make(),
                HostelsStaffPlugin::make(),
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

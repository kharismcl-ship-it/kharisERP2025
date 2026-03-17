<?php

namespace App\Providers\Filament;

use Alizharb\FilamentModuleManager\FilamentModuleManagerPlugin;
use App\Http\Middleware\EnsureGlobalSuperAdminRole;
use App\Http\Middleware\SyncSpatiePermissionsWithFilamentTenants;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\CommunicationCentre\Filament\CommunicationCentreFilamentPlugin;
use Modules\Construction\Filament\ConstructionFilamentPlugin;
use Modules\Core\Filament\CoreFilamentPlugin;
use Modules\Farms\Filament\FarmsFilamentPlugin;
use Modules\Finance\Filament\FinanceFilamentPlugin;
use Modules\Fleet\Filament\FleetFilamentPlugin;
use Modules\Hostels\Filament\HostelsFilamentPlugin;
use Modules\HR\Filament\HRFilamentPlugin;
use Modules\PaymentsChannel\Filament\PaymentsChannelFilamentPlugin;
use Modules\ManufacturingPaper\Filament\ManufacturingPaperFilamentPlugin;
use Modules\ManufacturingWater\Filament\ManufacturingWaterFilamentPlugin;
use Modules\ProcurementInventory\Filament\ProcurementInventoryFilamentPlugin;
use Modules\Sales\Filament\SalesFilamentPlugin;
use Modules\Requisition\Filament\RequisitionFilamentPlugin;
use Modules\ClientService\Filament\ClientServiceFilamentPlugin;
use Modules\ITSupport\Filament\ITSupportFilamentPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->default()
            ->brandName('Kharis ERP')
            ->databaseNotifications()
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                // Set Spatie team_id to NULL so global (unscoped) roles apply here
                SyncSpatiePermissionsWithFilamentTenants::class,
                // Propagate global super_admin into per-company roles (cached 24 h)
                EnsureGlobalSuperAdminRole::class,
            ])
            ->plugins([
                // centralApp(true) → Shield uses global unscoped roles for this panel
                FilamentShieldPlugin::make()
                    ->navigationGroup('Core')
                    ->centralApp(true),
                CoreFilamentPlugin::make(),
                HRFilamentPlugin::make(),
                HostelsFilamentPlugin::make(),
                PaymentsChannelFilamentPlugin::make(),
                FinanceFilamentPlugin::make(),
                CommunicationCentreFilamentPlugin::make(),
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
                FilamentModuleManagerPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

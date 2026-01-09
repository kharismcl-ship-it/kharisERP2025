<?php

namespace App\Providers\Filament;

use Alizharb\FilamentModuleManager\FilamentModuleManagerPlugin;
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
use Modules\Core\Models\Company;
use Modules\Finance\Filament\FinanceFilamentPlugin;
use Modules\Hostels\Filament\HostelsFilamentPlugin;
use Modules\PaymentsChannel\Filament\PaymentsChannelFilamentPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Register a minimal Filament admin panel at /admin.
        return $panel
            ->id('admin')
            ->path('admin')
            ->default()
            ->brandName('Kharis ERP')
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
           // ->tenant(Company::class, ownershipRelationship: 'companies')
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
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                HostelsFilamentPlugin::make(),
                PaymentsChannelFilamentPlugin::make(),
                FinanceFilamentPlugin::make(),
                FilamentModuleManagerPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

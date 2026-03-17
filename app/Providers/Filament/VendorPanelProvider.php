<?php

namespace App\Providers\Filament;

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
use Modules\ProcurementInventory\Filament\VendorPlugin;

class VendorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('vendor')
            ->path('vendor')
            ->login()
            ->brandName('Kharis ERP — Vendor Portal')
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Slate,
            ])

            // Vendor panel uses the vendor guard — no tenancy needed
            // (vendor is already scoped to a company via vendor_id)
            ->authGuard('vendor')

            ->discoverResources(
                in: base_path('Modules/ProcurementInventory/app/Filament/Vendor/Resources'),
                for: 'Modules\ProcurementInventory\Filament\Vendor\Resources'
            )
            ->discoverPages(
                in: base_path('Modules/ProcurementInventory/app/Filament/Vendor/Pages'),
                for: 'Modules\ProcurementInventory\Filament\Vendor\Pages'
            )
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
            ])

            ->plugins([
                VendorPlugin::make(),
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

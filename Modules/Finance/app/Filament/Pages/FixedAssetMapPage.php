<?php

namespace Modules\Finance\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class FixedAssetMapPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 50;

    use HasPageShield;

    protected static ?string $navigationLabel = 'Assets Map';

    protected string $view = 'finance::filament.pages.fixed-asset-map-page';
}

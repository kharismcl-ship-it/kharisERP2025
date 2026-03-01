<?php

namespace Modules\Core\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\AutomationSettingResource\Pages;
use Modules\Core\Filament\Resources\AutomationSettingResource\Schemas\AutomationSettingForm;
use Modules\Core\Filament\Resources\AutomationSettingResource\Tables\AutomationSettingTable;
use Modules\Core\Models\AutomationSetting;
use UnitEnum;

class AutomationSettingResource extends Resource
{
    protected static ?string $model = AutomationSetting::class;

    protected static ?string $slug = 'automation-settings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Core';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AutomationSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AutomationSettingTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutomationSettings::route('/'),
            'create' => Pages\CreateAutomationSetting::route('/create'),
            'view' => Pages\ViewAutomationSetting::route('/{record}'),
            'edit' => Pages\EditAutomationSetting::route('/{record}/edit'),
        ];
    }
}

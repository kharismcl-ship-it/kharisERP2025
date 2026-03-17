<?php

namespace Modules\Fleet\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Fleet\Models\TripLog;
use Modules\Fleet\Filament\Resources\Staff\MyTripLogResource\Schemas\TripLogInfolist;
use Modules\Fleet\Filament\Resources\Staff\MyTripLogResource\Tables\TripLogsTable;

class MyTripLogResource extends StaffGatedResource
{
    protected static ?string $model = TripLog::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'My Trips';

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 42;

    protected static ?string $slug = 'my-trips';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('driver_id', auth()->id())
            ->where('company_id', Filament::getTenant()?->id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TripLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Fleet\Filament\Resources\Staff\MyTripLogResource\Pages\ListMyTripLogs::route('/'),
            'view'  => \Modules\Fleet\Filament\Resources\Staff\MyTripLogResource\Pages\ViewMyTripLog::route('/{record}'),
        ];
    }
}

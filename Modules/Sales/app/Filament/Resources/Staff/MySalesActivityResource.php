<?php

namespace Modules\Sales\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Sales\Models\SalesActivity;
use Modules\Sales\Filament\Resources\Staff\MySalesActivityResource\Schemas\SalesActivityInfolist;
use Modules\Sales\Filament\Resources\Staff\MySalesActivityResource\Tables\SalesActivitiesTable;

class MySalesActivityResource extends StaffGatedResource
{
    protected static ?string $model = SalesActivity::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'My Activities';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 51;

    protected static ?string $slug = 'my-sales-activities';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('assigned_to', auth()->id())
            ->where('company_id', Filament::getTenant()?->id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalesActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesActivitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Sales\Filament\Resources\Staff\MySalesActivityResource\Pages\ListMySalesActivities::route('/'),
            'view'  => \Modules\Sales\Filament\Resources\Staff\MySalesActivityResource\Pages\ViewMySalesActivity::route('/{record}'),
        ];
    }
}

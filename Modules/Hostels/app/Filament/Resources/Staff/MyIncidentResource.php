<?php

namespace Modules\Hostels\Filament\Resources\Staff;

use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Schemas\IncidentForm;
use Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Schemas\IncidentInfolist;
use Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Tables\IncidentsTable;

class MyIncidentResource extends StaffGatedResource
{
    protected static ?string $model = Incident::class;

    // Incident has no company_id — scoped via reported_by_user_id
    protected static bool $isScopedToTenant = false;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'My Incidents';

    protected static string|\UnitEnum|null $navigationGroup = 'Hostel';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'my-incidents';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('reported_by_user_id', auth()->id())
            ->orderByDesc('reported_at');
    }

    public static function canEdit($record): bool
    {
        return $record->status === 'open';
    }

    public static function form(Schema $schema): Schema
    {
        return IncidentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IncidentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncidentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Pages\ListMyIncidents::route('/'),
            'create' => \Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Pages\CreateMyIncident::route('/create'),
            'view'   => \Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Pages\ViewMyIncident::route('/{record}'),
            'edit'   => \Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Pages\EditMyIncident::route('/{record}/edit'),
        ];
    }
}

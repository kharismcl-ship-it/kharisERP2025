<?php

namespace Modules\Hostels\Filament\Resources\Staff;

use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Schemas\MaintenanceRequestForm;
use Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Schemas\MaintenanceRequestInfolist;
use Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Tables\MaintenanceRequestsTable;

class MyMaintenanceRequestResource extends StaffGatedResource
{
    protected static ?string $model = MaintenanceRequest::class;

    // MaintenanceRequest has no company_id — scoped via reported_by_user_id
    protected static bool $isScopedToTenant = false;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedWrench;

    protected static ?string $navigationLabel = 'Maintenance Requests';

    protected static string|\UnitEnum|null $navigationGroup = 'Hostel';

    protected static ?int $navigationSort = 30;

    protected static ?string $slug = 'my-maintenance-requests';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('reported_by_user_id', auth()->id())
            ->orderByDesc('reported_at');
    }

    public static function canEdit($record): bool
    {
        return false; // read-only after submission — admin manages assignment/completion
    }

    public static function form(Schema $schema): Schema
    {
        return MaintenanceRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MaintenanceRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Pages\ListMyMaintenanceRequests::route('/'),
            'create' => \Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Pages\CreateMyMaintenanceRequest::route('/create'),
            'view'   => \Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Pages\ViewMyMaintenanceRequest::route('/{record}'),
        ];
    }
}

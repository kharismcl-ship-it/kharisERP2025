<?php

namespace Modules\Hostels\Filament\Resources\Staff;

use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Models\VisitorLog;
use Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Schemas\VisitorLogForm;
use Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Schemas\VisitorLogInfolist;
use Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Tables\VisitorLogsTable;

class MyVisitorLogResource extends StaffGatedResource
{
    protected static ?string $model = VisitorLog::class;

    // VisitorLog has no company_id — scoped via recorded_by_user_id
    protected static bool $isScopedToTenant = false;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Visitor Log';

    protected static string|\UnitEnum|null $navigationGroup = 'Hostel';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'my-visitor-logs';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('recorded_by_user_id', auth()->id())
            ->orderByDesc('check_in_at');
    }

    public static function form(Schema $schema): Schema
    {
        return VisitorLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VisitorLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitorLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Pages\ListMyVisitorLogs::route('/'),
            'create' => \Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Pages\CreateMyVisitorLog::route('/create'),
            'view'   => \Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Pages\ViewMyVisitorLog::route('/{record}'),
            'edit'   => \Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Pages\EditMyVisitorLog::route('/{record}/edit'),
        ];
    }
}

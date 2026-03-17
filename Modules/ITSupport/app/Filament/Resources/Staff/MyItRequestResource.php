<?php

namespace Modules\ITSupport\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\ITSupport\Models\ItRequest;
use Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Schemas\ItRequestForm;
use Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Schemas\ItRequestInfolist;
use Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Tables\ItRequestsTable;

class MyItRequestResource extends StaffGatedResource
{
    protected static ?string $model = ItRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $navigationLabel = 'My IT Requests';

    protected static string|\UnitEnum|null $navigationGroup = 'Requests';

    protected static ?int $navigationSort = 55;

    protected static ?string $slug = 'my-it-requests';

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()->where('requester_employee_id', $employee->id);
    }

    public static function form(Schema $schema): Schema
    {
        return ItRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Pages\ListMyItRequests::route('/'),
            'create' => \Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Pages\CreateMyItRequest::route('/create'),
            'view'   => \Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Pages\ViewMyItRequest::route('/{record}'),
        ];
    }
}

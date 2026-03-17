<?php

namespace Modules\ClientService\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\ClientService\Models\CsVisitor;
use Modules\HR\Models\Employee;
use Modules\ClientService\Filament\Resources\Staff\MyVisitorResource\Schemas\VisitorInfolist;
use Modules\ClientService\Filament\Resources\Staff\MyVisitorResource\Tables\VisitorsTable;

class MyVisitorResource extends StaffGatedResource
{
    protected static ?string $model = CsVisitor::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'My Visitors';

    protected static string|\UnitEnum|null $navigationGroup = 'Client Service';

    protected static ?int $navigationSort = 55;

    protected static ?string $slug = 'my-visitors';

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('host_employee_id', $employee->id)
            ->where('company_id', $companyId);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VisitorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitorsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\ClientService\Filament\Resources\Staff\MyVisitorResource\Pages\ListMyVisitors::route('/'),
            'view'  => \Modules\ClientService\Filament\Resources\Staff\MyVisitorResource\Pages\ViewMyVisitor::route('/{record}'),
        ];
    }
}

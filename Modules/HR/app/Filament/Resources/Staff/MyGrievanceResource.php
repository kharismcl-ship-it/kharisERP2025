<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\GrievanceCase;
use Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Schemas\GrievanceForm;
use Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Schemas\GrievanceInfolist;
use Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Tables\GrievancesTable;

class MyGrievanceResource extends StaffSelfServiceResource
{
    protected static ?string $model = GrievanceCase::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationCircle;

    protected static ?string $navigationLabel = 'My Grievances';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'my-grievances';

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()->where('employee_id', $employee->id);
    }

    public static function form(Schema $schema): Schema
    {
        return GrievanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GrievanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GrievancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Pages\ListMyGrievances::route('/'),
            'create' => \Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Pages\CreateMyGrievance::route('/create'),
            'view'   => \Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Pages\ViewMyGrievance::route('/{record}'),
        ];
    }
}

<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeGoal;
use Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Schemas\EmployeeGoalForm;
use Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Schemas\EmployeeGoalInfolist;
use Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Tables\EmployeeGoalsTable;

class MyEmployeeGoalResource extends StaffSelfServiceResource
{
    protected static ?string $model = EmployeeGoal::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'My Goals';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 58;

    protected static ?string $slug = 'my-goals';

    // EmployeeGoal has no company_id — scoped via employee_id in getEloquentQuery()
    protected static bool $isScopedToTenant = false;

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
            ->where('employee_id', $employee->id)
            ->orderByDesc('created_at');
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeGoalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeGoalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeGoalsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Pages\ListMyGoals::route('/'),
            'create' => \Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Pages\CreateMyGoal::route('/create'),
            'edit'   => \Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Pages\EditMyGoal::route('/{record}/edit'),
            'view'   => \Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Pages\ViewMyGoal::route('/{record}'),
        ];
    }
}

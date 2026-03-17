<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\PayrollLine;
use Modules\HR\Filament\Resources\Staff\MyPayslipResource\Schemas\PayslipInfolist;
use Modules\HR\Filament\Resources\Staff\MyPayslipResource\Tables\PayslipsTable;

class MyPayslipResource extends StaffSelfServiceResource
{
    protected static ?string $model = PayrollLine::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'My Payslips';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'my-payslips';

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
            ->with('payrollRun');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PayslipInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayslipsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\HR\Filament\Resources\Staff\MyPayslipResource\Pages\ListMyPayslips::route('/'),
            'view'  => \Modules\HR\Filament\Resources\Staff\MyPayslipResource\Pages\ViewMyPayslip::route('/{record}'),
        ];
    }
}

<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeLoan;
use Modules\HR\Filament\Resources\Staff\MyLoanResource\Schemas\LoanForm;
use Modules\HR\Filament\Resources\Staff\MyLoanResource\Schemas\LoanInfolist;
use Modules\HR\Filament\Resources\Staff\MyLoanResource\Tables\LoansTable;

class MyLoanResource extends StaffSelfServiceResource
{
    protected static ?string $model = EmployeeLoan::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'My Loans';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 35;

    protected static ?string $slug = 'my-loans';

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
        return LoanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoansTable::configure($table);
    }

    public static function canDelete($record): bool
    {
        return $record->status === 'pending';
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\Staff\MyLoanResource\Pages\ListMyLoans::route('/'),
            'create' => \Modules\HR\Filament\Resources\Staff\MyLoanResource\Pages\CreateMyLoan::route('/create'),
            'view'   => \Modules\HR\Filament\Resources\Staff\MyLoanResource\Pages\ViewMyLoan::route('/{record}'),
        ];
    }
}

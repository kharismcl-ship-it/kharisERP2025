<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Schemas\LeaveRequestForm;
use Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Schemas\LeaveRequestInfolist;
use Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Tables\LeaveRequestsTable;

class MyLeaveRequestResource extends StaffSelfServiceResource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'My Leave';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'my-leave';

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
        return LeaveRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaveRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveRequestsTable::configure($table);
    }



    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Pages\ListMyLeaveRequests::route('/'),
            'create' => \Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Pages\CreateMyLeaveRequest::route('/create'),
            'view'   => \Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Pages\ViewMyLeaveRequest::route('/{record}'),
            'edit'   => \Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Pages\EditMyLeaveRequest::route('/{record}/edit'),
        ];
    }
}

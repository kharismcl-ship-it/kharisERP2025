<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\AttendanceRecord;
use Modules\HR\Models\Employee;
use Modules\HR\Filament\Resources\Staff\MyAttendanceRecordResource\Schemas\AttendanceRecordInfolist;
use Modules\HR\Filament\Resources\Staff\MyAttendanceRecordResource\Tables\AttendanceRecordsTable;

class MyAttendanceRecordResource extends StaffSelfServiceResource
{
    protected static ?string $model = AttendanceRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'My Attendance';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 8;

    protected static ?string $slug = 'my-attendance';

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
            ->orderByDesc('date');
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttendanceRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceRecordsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\HR\Filament\Resources\Staff\MyAttendanceRecordResource\Pages\ListMyAttendanceRecords::route('/'),
            'view'  => \Modules\HR\Filament\Resources\Staff\MyAttendanceRecordResource\Pages\ViewMyAttendanceRecord::route('/{record}'),
        ];
    }
}

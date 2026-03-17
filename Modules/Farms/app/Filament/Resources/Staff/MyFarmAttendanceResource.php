<?php

namespace Modules\Farms\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Farms\Models\FarmWorker;
use Modules\Farms\Models\FarmWorkerAttendance;
use Modules\HR\Models\Employee;
use Modules\Farms\Filament\Resources\Staff\MyFarmAttendanceResource\Schemas\FarmAttendanceInfolist;
use Modules\Farms\Filament\Resources\Staff\MyFarmAttendanceResource\Tables\FarmAttendancesTable;

class MyFarmAttendanceResource extends StaffGatedResource
{
    protected static ?string $model = FarmWorkerAttendance::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'My Attendance';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm';

    protected static ?int $navigationSort = 30;

    protected static ?string $slug = 'my-farm-attendance';

    public static function getEloquentQuery(): Builder
    {
        $worker = self::resolveWorker();

        if (! $worker) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('farm_worker_id', $worker->id)
            ->orderByDesc('attendance_date');
    }

    public static function infolist(Schema $schema): Schema
    {
        return FarmAttendanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FarmAttendancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Farms\Filament\Resources\Staff\MyFarmAttendanceResource\Pages\ListMyFarmAttendances::route('/'),
            'view'  => \Modules\Farms\Filament\Resources\Staff\MyFarmAttendanceResource\Pages\ViewMyFarmAttendance::route('/{record}'),
        ];
    }

    public static function resolveWorker(): ?FarmWorker
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        return $employee
            ? FarmWorker::where('employee_id', $employee->id)
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->first()
            : null;
    }
}

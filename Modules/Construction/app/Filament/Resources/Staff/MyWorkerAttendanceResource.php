<?php

namespace Modules\Construction\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Construction\Models\ConstructionWorker;
use Modules\Construction\Models\WorkerAttendance;
use Modules\HR\Models\Employee;
use Modules\Construction\Filament\Resources\Staff\MyWorkerAttendanceResource\Schemas\WorkerAttendanceInfolist;
use Modules\Construction\Filament\Resources\Staff\MyWorkerAttendanceResource\Tables\WorkerAttendanceTable;

class MyWorkerAttendanceResource extends StaffGatedResource
{
    protected static ?string $model = WorkerAttendance::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'My Attendance';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 40;

    protected static ?string $slug = 'my-worker-attendance';

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        $worker = ConstructionWorker::where('employee_id', $employee->id)
            ->where('company_id', $companyId)
            ->first();

        if (! $worker) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()->where('construction_worker_id', $worker->id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkerAttendanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkerAttendanceTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Construction\Filament\Resources\Staff\MyWorkerAttendanceResource\Pages\ListMyWorkerAttendances::route('/'),
            'view'  => \Modules\Construction\Filament\Resources\Staff\MyWorkerAttendanceResource\Pages\ViewMyWorkerAttendance::route('/{record}'),
        ];
    }
}

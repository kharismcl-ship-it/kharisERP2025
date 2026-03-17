<?php

namespace Modules\Farms\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\FarmWorker;
use Modules\HR\Models\Employee;
use Modules\Farms\Filament\Resources\Staff\MyFarmTaskResource\Schemas\FarmTaskInfolist;
use Modules\Farms\Filament\Resources\Staff\MyFarmTaskResource\Tables\FarmTasksTable;

class MyFarmTaskResource extends StaffGatedResource
{
    protected static ?string $model = FarmTask::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'My Tasks';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'my-farm-tasks';

    public static function getEloquentQuery(): Builder
    {
        $worker = self::resolveWorker();

        if (! $worker) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('assigned_to_worker_id', $worker->id)
            ->orderByRaw('completed_at IS NOT NULL ASC')
            ->orderBy('due_date');
    }

    public static function infolist(Schema $schema): Schema
    {
        return FarmTaskInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FarmTasksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Farms\Filament\Resources\Staff\MyFarmTaskResource\Pages\ListMyFarmTasks::route('/'),
            'view'  => \Modules\Farms\Filament\Resources\Staff\MyFarmTaskResource\Pages\ViewMyFarmTask::route('/{record}'),
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

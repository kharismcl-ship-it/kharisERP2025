<?php

namespace Modules\Farms\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Farms\Models\FarmDailyReport;
use Modules\Farms\Models\FarmWorker;
use Modules\HR\Models\Employee;
use Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Schemas\FarmDailyReportForm;
use Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Schemas\FarmDailyReportInfolist;
use Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Tables\FarmDailyReportsTable;

class MyFarmDailyReportResource extends StaffGatedResource
{
    protected static ?string $model = FarmDailyReport::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'My Daily Reports';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'my-farm-daily-reports';

    public static function getEloquentQuery(): Builder
    {
        $worker = self::resolveWorker();

        if (! $worker) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('farm_worker_id', $worker->id)
            ->orderByDesc('report_date');
    }

    public static function canEdit($record): bool
    {
        return $record->status === 'draft';
    }

    public static function canDelete($record): bool
    {
        return $record->status === 'draft';
    }

    public static function form(Schema $schema): Schema
    {
        return FarmDailyReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FarmDailyReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FarmDailyReportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Pages\ListMyFarmDailyReports::route('/'),
            'create' => \Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Pages\CreateMyFarmDailyReport::route('/create'),
            'view'   => \Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Pages\ViewMyFarmDailyReport::route('/{record}'),
            'edit'   => \Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Pages\EditMyFarmDailyReport::route('/{record}/edit'),
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

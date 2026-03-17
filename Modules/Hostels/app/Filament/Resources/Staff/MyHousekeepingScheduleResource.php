<?php

namespace Modules\Hostels\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Models\HostelHousekeepingSchedule;
use Modules\HR\Models\Employee;
use Modules\Hostels\Filament\Resources\Staff\MyHousekeepingScheduleResource\Schemas\HousekeepingScheduleInfolist;
use Modules\Hostels\Filament\Resources\Staff\MyHousekeepingScheduleResource\Tables\HousekeepingSchedulesTable;

class MyHousekeepingScheduleResource extends StaffGatedResource
{
    protected static ?string $model = HostelHousekeepingSchedule::class;

    // No company_id — scoped via assigned_employee_id
    protected static bool $isScopedToTenant = false;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'My Schedule';

    protected static string|\UnitEnum|null $navigationGroup = 'Hostel';

    protected static ?int $navigationSort = 40;

    protected static ?string $slug = 'my-housekeeping-schedule';

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
            ->where('assigned_employee_id', $employee->id)
            ->orderBy('schedule_date');
    }

    public static function infolist(Schema $schema): Schema
    {
        return HousekeepingScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HousekeepingSchedulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Hostels\Filament\Resources\Staff\MyHousekeepingScheduleResource\Pages\ListMyHousekeepingSchedules::route('/'),
            'view'  => \Modules\Hostels\Filament\Resources\Staff\MyHousekeepingScheduleResource\Pages\ViewMyHousekeepingSchedule::route('/{record}'),
        ];
    }
}

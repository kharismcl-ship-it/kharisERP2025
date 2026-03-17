<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\ShiftAssignment;
use Modules\HR\Filament\Resources\Staff\MyShiftScheduleResource\Schemas\ShiftScheduleInfolist;
use Modules\HR\Filament\Resources\Staff\MyShiftScheduleResource\Tables\ShiftScheduleTable;

class MyShiftScheduleResource extends StaffSelfServiceResource
{
    protected static ?string $model = ShiftAssignment::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'My Shift';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'my-shift';

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
            ->where(fn ($q) => $q->whereNull('effective_to')->orWhere('effective_to', '>=', now()->toDateString()))
            ->orderBy('effective_from', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShiftScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShiftScheduleTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\HR\Filament\Resources\Staff\MyShiftScheduleResource\Pages\ListMyShiftSchedule::route('/'),
            'view'  => \Modules\HR\Filament\Resources\Staff\MyShiftScheduleResource\Pages\ViewMyShiftSchedule::route('/{record}'),
        ];
    }
}

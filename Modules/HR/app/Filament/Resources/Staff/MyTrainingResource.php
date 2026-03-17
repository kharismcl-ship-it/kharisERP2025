<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\TrainingNomination;
use Modules\HR\Filament\Resources\Staff\MyTrainingResource\Schemas\TrainingForm;
use Modules\HR\Filament\Resources\Staff\MyTrainingResource\Schemas\TrainingInfolist;
use Modules\HR\Filament\Resources\Staff\MyTrainingResource\Tables\TrainingTable;

class MyTrainingResource extends StaffSelfServiceResource
{
    protected static ?string $model = TrainingNomination::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'My Training';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 45;

    protected static ?string $slug = 'my-training';

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
        return TrainingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\Staff\MyTrainingResource\Pages\ListMyTraining::route('/'),
            'create' => \Modules\HR\Filament\Resources\Staff\MyTrainingResource\Pages\CreateMyTraining::route('/create'),
            'view'   => \Modules\HR\Filament\Resources\Staff\MyTrainingResource\Pages\ViewMyTraining::route('/{record}'),
        ];
    }
}

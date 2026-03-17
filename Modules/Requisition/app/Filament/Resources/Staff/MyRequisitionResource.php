<?php

namespace Modules\Requisition\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Schemas\RequisitionForm;
use Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Schemas\RequisitionInfolist;
use Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Tables\RequisitionsTable;

class MyRequisitionResource extends StaffGatedResource
{
    protected static ?string $model = Requisition::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'My Requisitions';

    protected static string|\UnitEnum|null $navigationGroup = 'Requests';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'my-requisitions';

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()->where('requester_employee_id', $employee->id);
    }

    public static function form(Schema $schema): Schema
    {
        return RequisitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RequisitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequisitionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Pages\ListMyRequisitions::route('/'),
            'create' => \Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Pages\CreateMyRequisition::route('/create'),
            'view'   => \Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Pages\ViewMyRequisition::route('/{record}'),
        ];
    }
}

<?php

namespace Modules\Farms\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Farms\Models\FarmRequest;
use Modules\Farms\Models\FarmWorker;
use Modules\HR\Models\Employee;
use Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Schemas\FarmRequestForm;
use Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Schemas\FarmRequestInfolist;
use Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Tables\FarmRequestsTable;

class MyFarmRequestResource extends StaffGatedResource
{
    protected static ?string $model = FarmRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'My Farm Requests';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm';

    protected static ?int $navigationSort = 40;

    protected static ?string $slug = 'my-farm-requests';

    public static function getEloquentQuery(): Builder
    {
        $worker = self::resolveWorker();

        if (! $worker) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('requested_by', $worker->id)
            ->orderByDesc('created_at');
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
        return FarmRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FarmRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FarmRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Pages\ListMyFarmRequests::route('/'),
            'create' => \Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Pages\CreateMyFarmRequest::route('/create'),
            'view'   => \Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Pages\ViewMyFarmRequest::route('/{record}'),
            'edit'   => \Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Pages\EditMyFarmRequest::route('/{record}/edit'),
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

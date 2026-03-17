<?php

namespace Modules\Finance\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Finance\Models\FixedAsset;
use Modules\HR\Models\Employee;
use Modules\Finance\Filament\Resources\Staff\MyFixedAssetResource\Schemas\FixedAssetInfolist;
use Modules\Finance\Filament\Resources\Staff\MyFixedAssetResource\Tables\FixedAssetsTable;

class MyFixedAssetResource extends StaffGatedResource
{
    protected static ?string $model = FixedAsset::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $navigationLabel = 'My Assets';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 70;

    protected static ?string $slug = 'my-fixed-assets';

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
            ->where('custodian_employee_id', $employee->id)
            ->where('company_id', $companyId);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FixedAssetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FixedAssetsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Finance\Filament\Resources\Staff\MyFixedAssetResource\Pages\ListMyFixedAssets::route('/'),
            'view'  => \Modules\Finance\Filament\Resources\Staff\MyFixedAssetResource\Pages\ViewMyFixedAsset::route('/{record}'),
        ];
    }
}

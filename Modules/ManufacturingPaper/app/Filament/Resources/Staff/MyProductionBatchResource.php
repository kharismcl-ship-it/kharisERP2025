<?php

namespace Modules\ManufacturingPaper\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\ManufacturingPaper\Models\MpProductionBatch;
use Modules\ManufacturingPaper\Filament\Resources\Staff\MyProductionBatchResource\Schemas\ProductionBatchInfolist;
use Modules\ManufacturingPaper\Filament\Resources\Staff\MyProductionBatchResource\Tables\ProductionBatchesTable;

class MyProductionBatchResource extends StaffGatedResource
{
    protected static ?string $model = MpProductionBatch::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Production Batches';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing';

    protected static ?int $navigationSort = 60;

    protected static ?string $slug = 'my-production-batches';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', Filament::getTenant()?->id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductionBatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionBatchesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\ManufacturingPaper\Filament\Resources\Staff\MyProductionBatchResource\Pages\ListMyProductionBatches::route('/'),
            'view'  => \Modules\ManufacturingPaper\Filament\Resources\Staff\MyProductionBatchResource\Pages\ViewMyProductionBatch::route('/{record}'),
        ];
    }
}

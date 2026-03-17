<?php

namespace Modules\ManufacturingWater\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\ManufacturingWater\Models\MwWaterTestRecord;
use Modules\ManufacturingWater\Filament\Resources\Staff\MyWaterTestResource\Schemas\WaterTestInfolist;
use Modules\ManufacturingWater\Filament\Resources\Staff\MyWaterTestResource\Tables\WaterTestsTable;

class MyWaterTestResource extends StaffGatedResource
{
    protected static ?string $model = MwWaterTestRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $navigationLabel = 'Water Tests';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing';

    protected static ?int $navigationSort = 61;

    protected static ?string $slug = 'my-water-tests';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', Filament::getTenant()?->id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WaterTestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WaterTestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\ManufacturingWater\Filament\Resources\Staff\MyWaterTestResource\Pages\ListMyWaterTests::route('/'),
            'view'  => \Modules\ManufacturingWater\Filament\Resources\Staff\MyWaterTestResource\Pages\ViewMyWaterTest::route('/{record}'),
        ];
    }
}

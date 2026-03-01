<?php

namespace Modules\ManufacturingWater\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource\Pages;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource\RelationManagers;
use Modules\ManufacturingWater\Models\MwPlant;

class MwPlantResource extends Resource
{
    protected static ?string $model = MwPlant::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Water';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plant Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('location')->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    Select::make('type')
                        ->options(array_combine(MwPlant::TYPES, array_map('ucfirst', MwPlant::TYPES)))
                        ->default('treatment')
                        ->required(),
                    Select::make('source_type')
                        ->label('Water Source')
                        ->options(array_combine(MwPlant::SOURCE_TYPES, array_map('ucfirst', MwPlant::SOURCE_TYPES)))
                        ->nullable(),
                    Select::make('status')
                        ->options(array_combine(MwPlant::STATUSES, array_map('ucfirst', MwPlant::STATUSES)))
                        ->default('active')
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('capacity_liters_per_day')->label('Capacity (Litres/Day)')->numeric()->step(0.01),
                    TextInput::make('slug')->maxLength(255),
                ]),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('location'),
                TextColumn::make('type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'treatment'    => 'primary',
                        'bottling'     => 'success',
                        'distribution' => 'info',
                        default        => 'gray',
                    }),
                TextColumn::make('source_type')->label('Source'),
                TextColumn::make('capacity_liters_per_day')->label('Capacity (L/day)')->numeric(decimalPlaces: 0),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'         => 'success',
                        'idle'           => 'warning',
                        'maintenance'    => 'danger',
                        'decommissioned' => 'gray',
                        default          => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(MwPlant::TYPES, array_map('ucfirst', MwPlant::TYPES))),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(MwPlant::STATUSES, array_map('ucfirst', MwPlant::STATUSES))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TreatmentStagesRelationManager::class,
            RelationManagers\WaterTestRecordsRelationManager::class,
            RelationManagers\TankLevelsRelationManager::class,
            RelationManagers\ChemicalUsagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMwPlants::route('/'),
            'create' => Pages\CreateMwPlant::route('/create'),
            'view'   => Pages\ViewMwPlant::route('/{record}'),
            'edit'   => Pages\EditMwPlant::route('/{record}/edit'),
        ];
    }
}

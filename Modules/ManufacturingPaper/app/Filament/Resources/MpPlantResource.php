<?php

namespace Modules\ManufacturingPaper\Filament\Resources;

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
use Modules\ManufacturingPaper\Filament\Resources\MpPlantResource\Pages;
use Modules\ManufacturingPaper\Filament\Resources\MpPlantResource\RelationManagers;
use Modules\ManufacturingPaper\Models\MpPlant;

class MpPlantResource extends Resource
{
    protected static ?string $model = MpPlant::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Paper';

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
                        ->options(array_combine(MpPlant::TYPES, array_map('ucwords', array_map(fn ($t) => str_replace('_', ' ', $t), MpPlant::TYPES))))
                        ->default('integrated')
                        ->required(),
                    Select::make('status')
                        ->options(array_combine(MpPlant::STATUSES, array_map('ucfirst', MpPlant::STATUSES)))
                        ->default('active')
                        ->required(),
                    TextInput::make('slug')->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('capacity')->numeric()->step(0.01)->label('Capacity'),
                    Select::make('capacity_unit')
                        ->options(['tonnes/day' => 'Tonnes/Day', 'kg/day' => 'Kg/Day', 'reams/day' => 'Reams/Day'])
                        ->default('tonnes/day'),
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
                        'integrated' => 'success',
                        'pulp_only'  => 'info',
                        'paper_only' => 'primary',
                        default      => 'gray',
                    }),
                TextColumn::make('capacity')->numeric(decimalPlaces: 2),
                TextColumn::make('capacity_unit')->label('Unit'),
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
                    ->options(array_combine(MpPlant::TYPES, array_map('ucwords', array_map(fn ($t) => str_replace('_', ' ', $t), MpPlant::TYPES)))),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(MpPlant::STATUSES, array_map('ucfirst', MpPlant::STATUSES))),
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
            RelationManagers\ProductionLinesRelationManager::class,
            RelationManagers\ProductionBatchesRelationManager::class,
            RelationManagers\EquipmentLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMpPlants::route('/'),
            'create' => Pages\CreateMpPlant::route('/create'),
            'view'   => Pages\ViewMpPlant::route('/{record}'),
            'edit'   => Pages\EditMpPlant::route('/{record}/edit'),
        ];
    }
}

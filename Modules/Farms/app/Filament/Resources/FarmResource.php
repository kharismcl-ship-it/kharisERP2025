<?php

namespace Modules\Farms\Filament\Resources;

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
use Modules\Farms\Filament\Resources\FarmResource\Pages;
use Modules\Farms\Filament\Resources\FarmResource\RelationManagers;
use Modules\Farms\Models\Farm;

class FarmResource extends Resource
{
    protected static ?string $model = Farm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sun';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Farm Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('location')->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    Select::make('type')
                        ->options(array_combine(Farm::TYPES, array_map('ucfirst', Farm::TYPES)))
                        ->default('mixed')
                        ->required(),
                    TextInput::make('total_area')->label('Total Area')->numeric()->step(0.0001),
                    Select::make('area_unit')
                        ->options(['acres' => 'Acres', 'hectares' => 'Hectares'])
                        ->default('acres'),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('owner_name')->label('Owner Name')->maxLength(255),
                    TextInput::make('owner_phone')->label('Owner Phone')->maxLength(50),
                ]),
                Select::make('status')
                    ->options(array_combine(Farm::STATUSES, array_map('ucfirst', Farm::STATUSES)))
                    ->default('active'),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('location'),
                TextColumn::make('type')->badge(),
                TextColumn::make('total_area')->label('Area')->numeric(decimalPlaces: 2),
                TextColumn::make('area_unit')->label('Unit'),
                TextColumn::make('owner_name')->label('Owner'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'gray',
                        'fallow'   => 'warning',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(Farm::TYPES, array_map('ucfirst', Farm::TYPES))),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(Farm::STATUSES, array_map('ucfirst', Farm::STATUSES))),
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
            RelationManagers\CropCyclesRelationManager::class,
            RelationManagers\LivestockBatchesRelationManager::class,
            RelationManagers\HarvestRecordsRelationManager::class,
            RelationManagers\FarmExpensesRelationManager::class,
            RelationManagers\FarmPlotsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarms::route('/'),
            'create' => Pages\CreateFarm::route('/create'),
            'view'   => Pages\ViewFarm::route('/{record}'),
            'edit'   => Pages\EditFarm::route('/{record}/edit'),
        ];
    }
}

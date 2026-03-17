<?php

namespace Modules\ManufacturingWater\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ManufacturingWater\Filament\Resources\MwTankLevelResource\Pages;
use Modules\ManufacturingWater\Models\MwTankLevel;

class MwTankLevelResource extends Resource
{
    protected static ?string $model = MwTankLevel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Water';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Tank Levels';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tank Reading')->schema([
                Grid::make(2)->schema([
                    Select::make('plant_id')
                        ->label('Plant')
                        ->relationship('plant', 'name')
                        ->searchable()
                        ->required(),
                    TextInput::make('tank_name')->required()->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('capacity_liters')
                        ->label('Capacity (L)')
                        ->numeric()
                        ->step(0.01)
                        ->required(),
                    TextInput::make('current_level_liters')
                        ->label('Current Level (L)')
                        ->numeric()
                        ->step(0.01)
                        ->required(),
                    DateTimePicker::make('recorded_at')
                        ->label('Recorded At')
                        ->default(now())
                        ->required(),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tank Reading')->columns(3)->schema([
                TextEntry::make('plant.name')->label('Plant'),
                TextEntry::make('tank_name')->label('Tank Name'),
                TextEntry::make('recorded_at')->label('Recorded At')->dateTime(),
                TextEntry::make('capacity_liters')->label('Capacity (L)'),
                TextEntry::make('current_level_liters')->label('Current Level (L)'),
                TextEntry::make('fill_percentage')
                    ->label('Fill %')
                    ->getStateUsing(fn ($record) => $record->fill_percentage . '%')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->fill_percentage < 20 => 'danger',
                        $record->fill_percentage < 50 => 'warning',
                        default                       => 'success',
                    }),
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plant.name')->label('Plant')->searchable()->sortable(),
                TextColumn::make('tank_name')->label('Tank')->searchable()->sortable(),
                TextColumn::make('capacity_liters')->label('Capacity (L)')->numeric(decimalPlaces: 0),
                TextColumn::make('current_level_liters')->label('Current (L)')->numeric(decimalPlaces: 0),
                TextColumn::make('fill_percentage')->label('Fill %')
                    ->getStateUsing(fn ($record) => $record->fill_percentage . '%')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->fill_percentage < 20 => 'danger',
                        $record->fill_percentage < 50 => 'warning',
                        default                       => 'success',
                    }),
                TextColumn::make('recorded_at')->label('Recorded')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plant_id')
                    ->label('Plant')
                    ->relationship('plant', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ])
            ->defaultSort('recorded_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMwTankLevels::route('/'),
            'create' => Pages\CreateMwTankLevel::route('/create'),
            'view'   => Pages\ViewMwTankLevel::route('/{record}'),
            'edit'   => Pages\EditMwTankLevel::route('/{record}/edit'),
        ];
    }
}

<?php

namespace Modules\ManufacturingPaper\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionLineResource\Pages;
use Modules\ManufacturingPaper\Models\MpProductionLine;
use Modules\ManufacturingPaper\Models\MpPlant;

class MpProductionLineResource extends Resource
{
    protected static ?string $model = MpProductionLine::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Paper';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Production Lines';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Line Details')->schema([
                Grid::make(2)->schema([
                    Select::make('plant_id')
                        ->label('Plant')
                        ->relationship('plant', 'name')
                        ->searchable()
                        ->required(),
                    TextInput::make('name')->required()->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    Select::make('line_type')
                        ->label('Line Type')
                        ->options(array_combine(
                            MpProductionLine::LINE_TYPES,
                            array_map('ucfirst', MpProductionLine::LINE_TYPES)
                        ))
                        ->required(),
                    Select::make('status')
                        ->options(array_combine(
                            MpProductionLine::STATUSES,
                            array_map('ucfirst', MpProductionLine::STATUSES)
                        ))
                        ->default('operational')
                        ->required(),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('capacity_per_day')
                        ->label('Capacity per Day')
                        ->numeric()
                        ->step(0.01),
                    Select::make('capacity_unit')
                        ->options([
                            'tonnes/day' => 'Tonnes/Day',
                            'kg/day'     => 'Kg/Day',
                            'reams/day'  => 'Reams/Day',
                        ])
                        ->default('tonnes/day'),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plant.name')->label('Plant')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('line_type')->label('Type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paper'    => 'success',
                        'pulp'     => 'info',
                        'coating'  => 'primary',
                        'finishing'=> 'warning',
                        default    => 'gray',
                    }),
                TextColumn::make('capacity_per_day')->label('Capacity/Day')->numeric(decimalPlaces: 2),
                TextColumn::make('capacity_unit')->label('Unit'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'operational'    => 'success',
                        'idle'           => 'warning',
                        'maintenance'    => 'danger',
                        'decommissioned' => 'gray',
                        default          => 'gray',
                    }),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plant_id')
                    ->label('Plant')
                    ->relationship('plant', 'name'),
                Tables\Filters\SelectFilter::make('line_type')
                    ->label('Line Type')
                    ->options(array_combine(
                        MpProductionLine::LINE_TYPES,
                        array_map('ucfirst', MpProductionLine::LINE_TYPES)
                    )),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        MpProductionLine::STATUSES,
                        array_map('ucfirst', MpProductionLine::STATUSES)
                    )),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMpProductionLines::route('/'),
            'create' => Pages\CreateMpProductionLine::route('/create'),
            'view'   => Pages\ViewMpProductionLine::route('/{record}'),
            'edit'   => Pages\EditMpProductionLine::route('/{record}/edit'),
        ];
    }
}

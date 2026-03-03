<?php

namespace Modules\ManufacturingWater\Filament\Resources;

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
use Modules\ManufacturingWater\Filament\Resources\MwTreatmentStageResource\Pages;
use Modules\ManufacturingWater\Models\MwTreatmentStage;

class MwTreatmentStageResource extends Resource
{
    protected static ?string $model = MwTreatmentStage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Water';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Treatment Stages';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Stage Details')->schema([
                Grid::make(2)->schema([
                    Select::make('plant_id')
                        ->label('Plant')
                        ->relationship('plant', 'name')
                        ->searchable()
                        ->required(),
                    TextInput::make('name')->required()->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('stage_order')
                        ->label('Order')
                        ->integer()
                        ->minValue(1)
                        ->required(),
                    Select::make('stage_type')
                        ->label('Stage Type')
                        ->options(array_combine(
                            MwTreatmentStage::STAGE_TYPES,
                            MwTreatmentStage::STAGE_TYPES
                        ))
                        ->required(),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ]),
                Textarea::make('description')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plant.name')->label('Plant')->searchable()->sortable(),
                TextColumn::make('stage_order')->label('#')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('stage_type')->label('Type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'chlorination' => 'danger',
                        'filtration'   => 'info',
                        'UV'           => 'warning',
                        'RO'           => 'primary',
                        default        => 'gray',
                    }),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plant_id')
                    ->label('Plant')
                    ->relationship('plant', 'name'),
                Tables\Filters\SelectFilter::make('stage_type')
                    ->label('Stage Type')
                    ->options(array_combine(
                        MwTreatmentStage::STAGE_TYPES,
                        MwTreatmentStage::STAGE_TYPES
                    )),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ])
            ->defaultSort('stage_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMwTreatmentStages::route('/'),
            'create' => Pages\CreateMwTreatmentStage::route('/create'),
            'view'   => Pages\ViewMwTreatmentStage::route('/{record}'),
            'edit'   => Pages\EditMwTreatmentStage::route('/{record}/edit'),
        ];
    }
}

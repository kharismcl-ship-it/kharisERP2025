<?php

namespace Modules\ManufacturingPaper\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
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
use Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource\Pages;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource\RelationManagers;
use Modules\ManufacturingPaper\Models\MpProductionBatch;

class MpProductionBatchResource extends Resource
{
    protected static ?string $model = MpProductionBatch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Paper';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Batch Details')->schema([
                Grid::make(3)->schema([
                    Select::make('plant_id')
                        ->label('Plant')
                        ->relationship('plant', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('production_line_id')
                        ->label('Production Line')
                        ->relationship('productionLine', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('paper_grade_id')
                        ->label('Paper Grade')
                        ->relationship('paperGrade', 'name')
                        ->searchable()
                        ->required(),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('batch_number')->label('Batch Number')->maxLength(50)->helperText('Auto-generated if blank'),
                    Select::make('status')
                        ->options(array_combine(MpProductionBatch::STATUSES, array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), MpProductionBatch::STATUSES))))
                        ->default('planned')
                        ->required(),
                    Select::make('unit')
                        ->options(['tonnes' => 'Tonnes', 'kg' => 'Kg', 'reams' => 'Reams'])
                        ->default('tonnes'),
                ]),
            ]),

            Section::make('Quantities')->schema([
                Grid::make(3)->schema([
                    TextInput::make('quantity_planned')->label('Planned Quantity')->numeric()->step(0.001)->required(),
                    TextInput::make('quantity_produced')->label('Produced Quantity')->numeric()->step(0.001)->default(0),
                    TextInput::make('waste_quantity')->label('Waste Quantity')->numeric()->step(0.001)->default(0),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('raw_material_used')->label('Raw Material Used')->numeric()->step(0.001),
                    TextInput::make('production_cost')->label('Production Cost')->numeric()->prefix('GHS')->step(0.01),
                ]),
            ]),

            Section::make('Timeline')->schema([
                Grid::make(2)->schema([
                    DateTimePicker::make('start_time')->label('Start Time'),
                    DateTimePicker::make('end_time')->label('End Time'),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_number')->label('Batch No.')->searchable()->sortable(),
                TextColumn::make('plant.name')->label('Plant')->searchable(),
                TextColumn::make('productionLine.name')->label('Line'),
                TextColumn::make('paperGrade.name')->label('Grade'),
                TextColumn::make('quantity_planned')->label('Planned')->numeric(decimalPlaces: 2),
                TextColumn::make('quantity_produced')->label('Produced')->numeric(decimalPlaces: 2),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'planned'     => 'info',
                        'on_hold'     => 'gray',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('start_time')->dateTime()->sortable()->label('Started'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(MpProductionBatch::STATUSES, array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), MpProductionBatch::STATUSES)))),
                Tables\Filters\SelectFilter::make('plant_id')
                    ->label('Plant')
                    ->relationship('plant', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QualityRecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMpProductionBatches::route('/'),
            'create' => Pages\CreateMpProductionBatch::route('/create'),
            'view'   => Pages\ViewMpProductionBatch::route('/{record}'),
            'edit'   => Pages\EditMpProductionBatch::route('/{record}/edit'),
        ];
    }
}

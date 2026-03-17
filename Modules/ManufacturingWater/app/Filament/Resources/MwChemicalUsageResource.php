<?php

namespace Modules\ManufacturingWater\Filament\Resources;

use Filament\Forms\Components\DatePicker;
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
use Modules\ManufacturingWater\Filament\Resources\MwChemicalUsageResource\Pages;
use Modules\ManufacturingWater\Models\MwChemicalUsage;

class MwChemicalUsageResource extends Resource
{
    protected static ?string $model = MwChemicalUsage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-eye-dropper';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Water';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Chemical Usage';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Usage Details')->schema([
                Grid::make(2)->schema([
                    Select::make('plant_id')
                        ->label('Plant')
                        ->relationship('plant', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('treatment_stage_id')
                        ->label('Treatment Stage')
                        ->relationship('treatmentStage', 'name')
                        ->searchable()
                        ->nullable(),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('chemical_name')->required()->maxLength(255),
                    DatePicker::make('usage_date')->required()->default(now()),
                    TextInput::make('batch_number')->maxLength(100)->nullable(),
                ]),
                Grid::make(4)->schema([
                    TextInput::make('quantity')->numeric()->step(0.001)->required(),
                    TextInput::make('unit')->maxLength(50)->default('kg'),
                    TextInput::make('unit_cost')->label('Unit Cost')->numeric()->prefix('GHS')->step(0.0001),
                    TextInput::make('total_cost')->label('Total Cost')->numeric()->prefix('GHS')->disabled()->dehydrated(false),
                ]),
                TextInput::make('purpose')->maxLength(255)->nullable(),
                Textarea::make('notes')->rows(2)->nullable()->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Usage Details')->columns(3)->schema([
                TextEntry::make('plant.name')->label('Plant'),
                TextEntry::make('treatmentStage.name')->label('Treatment Stage')->placeholder('—'),
                TextEntry::make('chemical_name')->label('Chemical'),
                TextEntry::make('usage_date')->date(),
                TextEntry::make('batch_number')->label('Batch No.')->placeholder('—'),
                TextEntry::make('purpose')->placeholder('—'),
                TextEntry::make('quantity'),
                TextEntry::make('unit'),
                TextEntry::make('total_cost')->label('Total Cost')->money('GHS'),
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plant.name')->label('Plant')->searchable()->sortable(),
                TextColumn::make('chemical_name')->label('Chemical')->searchable()->sortable(),
                TextColumn::make('treatmentStage.name')->label('Stage')->placeholder('—'),
                TextColumn::make('usage_date')->label('Date')->date()->sortable(),
                TextColumn::make('quantity')->numeric(decimalPlaces: 3),
                TextColumn::make('unit'),
                TextColumn::make('total_cost')->label('Total Cost')->money('GHS'),
                TextColumn::make('purpose')->placeholder('—')->limit(40),
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
            ->defaultSort('usage_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMwChemicalUsages::route('/'),
            'create' => Pages\CreateMwChemicalUsage::route('/create'),
            'view'   => Pages\ViewMwChemicalUsage::route('/{record}'),
            'edit'   => Pages\EditMwChemicalUsage::route('/{record}/edit'),
        ];
    }
}

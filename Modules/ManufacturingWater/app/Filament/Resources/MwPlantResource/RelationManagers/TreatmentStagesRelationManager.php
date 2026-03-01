<?php

namespace Modules\ManufacturingWater\Filament\Resources\MwPlantResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ManufacturingWater\Models\MwTreatmentStage;

class TreatmentStagesRelationManager extends RelationManager
{
    protected static string $relationship = 'treatmentStages';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('stage_order')->numeric()->required()->default(1)->label('Order'),
                Select::make('stage_type')
                    ->options(array_combine(MwTreatmentStage::STAGE_TYPES, MwTreatmentStage::STAGE_TYPES))
                    ->required(),
            ]),
            Toggle::make('is_active')->default(true)->inline(false),
            Textarea::make('description')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('stage_order')
            ->columns([
                TextColumn::make('stage_order')->label('#')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('stage_type')->label('Type')->badge(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->defaultSort('stage_order')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}

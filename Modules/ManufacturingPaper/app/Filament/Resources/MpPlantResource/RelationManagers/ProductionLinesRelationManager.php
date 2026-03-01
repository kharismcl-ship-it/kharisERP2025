<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpPlantResource\RelationManagers;

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
use Modules\ManufacturingPaper\Models\MpProductionLine;

class ProductionLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'productionLines';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                Select::make('line_type')
                    ->options(array_combine(MpProductionLine::LINE_TYPES, array_map('ucfirst', MpProductionLine::LINE_TYPES)))
                    ->required(),
            ]),
            Grid::make(3)->schema([
                TextInput::make('capacity_per_day')->numeric()->step(0.01)->label('Capacity/Day'),
                Select::make('capacity_unit')->options(['tonnes' => 'Tonnes', 'kg' => 'Kg', 'reams' => 'Reams'])->default('tonnes'),
                Select::make('status')
                    ->options(array_combine(MpProductionLine::STATUSES, array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), MpProductionLine::STATUSES))))
                    ->default('operational'),
            ]),
            Toggle::make('is_active')->default(true)->inline(false),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('line_type')->label('Type')->badge(),
                TextColumn::make('capacity_per_day')->label('Capacity/Day')->numeric(decimalPlaces: 2),
                TextColumn::make('capacity_unit')->label('Unit'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'operational'    => 'success',
                        'maintenance'    => 'warning',
                        'idle'           => 'gray',
                        'decommissioned' => 'danger',
                        default          => 'gray',
                    }),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
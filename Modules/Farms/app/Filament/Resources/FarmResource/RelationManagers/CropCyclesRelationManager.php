<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\CropCycle;

class CropCyclesRelationManager extends RelationManager
{
    protected static string $relationship = 'cropCycles';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('crop_name')->required()->maxLength(255),
            TextInput::make('variety')->maxLength(255),
            TextInput::make('season')->maxLength(100),
            Select::make('farm_plot_id')->label('Plot')->relationship('plot', 'name')->searchable()->nullable(),
            DatePicker::make('planting_date')->required(),
            DatePicker::make('expected_harvest_date')->label('Expected Harvest'),
            DatePicker::make('actual_harvest_date')->label('Actual Harvest'),
            TextInput::make('planted_area')->label('Planted Area')->numeric()->step(0.0001),
            Select::make('planted_area_unit')->options(['acres' => 'Acres', 'hectares' => 'Hectares'])->default('acres'),
            TextInput::make('expected_yield')->label('Expected Yield')->numeric()->step(0.001),
            TextInput::make('yield_unit')->label('Yield Unit')->maxLength(50),
            Select::make('status')->options(array_combine(CropCycle::STATUSES, array_map('ucfirst', CropCycle::STATUSES)))->default('growing'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('crop_name')->label('Crop')->searchable(),
                TextColumn::make('variety'),
                TextColumn::make('season'),
                TextColumn::make('planting_date')->date()->sortable(),
                TextColumn::make('expected_harvest_date')->date()->label('Expected Harvest'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'harvested' => 'success',
                        'growing'   => 'info',
                        'preparing' => 'gray',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('planting_date', 'desc');
    }
}

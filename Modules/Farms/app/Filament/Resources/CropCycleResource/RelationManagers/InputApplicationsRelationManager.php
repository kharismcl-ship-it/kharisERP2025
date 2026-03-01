<?php

namespace Modules\Farms\Filament\Resources\CropCycleResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\CropInputApplication;

class InputApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'inputApplications';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('input_type')
                ->options(array_combine(
                    CropInputApplication::INPUT_TYPES,
                    array_map('ucfirst', CropInputApplication::INPUT_TYPES)
                ))
                ->required(),
            DatePicker::make('application_date')->required(),
            TextInput::make('product_name')->required()->maxLength(255),
            TextInput::make('quantity')->required()->numeric()->step(0.001),
            TextInput::make('unit')->maxLength(50)->placeholder('kg, L, bags'),
            TextInput::make('unit_cost')->label('Unit Cost')->numeric()->prefix('GHS')->step(0.0001),
            TextInput::make('total_cost')->label('Total Cost')->numeric()->prefix('GHS')->step(0.01)
                ->helperText('Auto-calculated from quantity × unit cost.'),
            TextInput::make('application_method')->maxLength(255)->placeholder('e.g. Foliar spray, Soil drench'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application_date')->date('d M Y')->sortable(),
                TextColumn::make('input_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fertilizer' => 'success',
                        'pesticide'  => 'danger',
                        'herbicide'  => 'warning',
                        'fungicide'  => 'info',
                        default      => 'gray',
                    }),
                TextColumn::make('product_name')->searchable(),
                TextColumn::make('quantity')->numeric(decimalPlaces: 2),
                TextColumn::make('unit')->placeholder('—'),
                TextColumn::make('unit_cost')->money('GHS')->label('Unit Cost')->placeholder('—'),
                TextColumn::make('total_cost')->money('GHS')->sortable(),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('application_date', 'desc');
    }
}
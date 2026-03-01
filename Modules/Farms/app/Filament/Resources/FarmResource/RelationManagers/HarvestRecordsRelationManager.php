<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class HarvestRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'harvestRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('harvest_date')->required(),
            Select::make('crop_cycle_id')->label('Crop Cycle')->relationship('cropCycle', 'crop_name')->searchable()->nullable(),
            TextInput::make('quantity')->required()->numeric()->step(0.001),
            TextInput::make('unit')->required()->maxLength(50),
            TextInput::make('unit_price')->label('Unit Price')->numeric()->prefix('GHS')->step(0.0001),
            TextInput::make('total_revenue')->label('Total Revenue')->numeric()->prefix('GHS')->step(0.01),
            TextInput::make('buyer_name')->label('Buyer')->maxLength(255),
            TextInput::make('storage_location')->label('Storage Location')->maxLength(255),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('harvest_date')->date()->sortable(),
                TextColumn::make('cropCycle.crop_name')->label('Crop'),
                TextColumn::make('quantity')->numeric(decimalPlaces: 2),
                TextColumn::make('unit'),
                TextColumn::make('unit_price')->money('GHS')->label('Unit Price'),
                TextColumn::make('total_revenue')->money('GHS')->sortable(),
                TextColumn::make('buyer_name')->label('Buyer'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('harvest_date', 'desc');
    }
}

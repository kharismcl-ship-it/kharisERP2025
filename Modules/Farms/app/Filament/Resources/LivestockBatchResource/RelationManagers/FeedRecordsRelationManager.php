<?php

namespace Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class FeedRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'feedRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('feed_date')->required(),
            TextInput::make('feed_type')->required()->maxLength(255),
            TextInput::make('quantity_kg')->label('Quantity (kg)')->required()->numeric()->step(0.001),
            TextInput::make('unit_cost')->label('Unit Cost (GHS/kg)')->numeric()->prefix('GHS')->step(0.0001),
            TextInput::make('total_cost')->label('Total Cost')->numeric()->prefix('GHS')->step(0.01)
                ->helperText('Auto-calculated from quantity × unit cost.'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('feed_date')->date('d M Y')->sortable(),
                TextColumn::make('feed_type'),
                TextColumn::make('quantity_kg')->label('Qty (kg)')->numeric(decimalPlaces: 2),
                TextColumn::make('unit_cost')->money('GHS')->label('Unit Cost'),
                TextColumn::make('total_cost')->money('GHS')->sortable(),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('feed_date', 'desc');
    }
}

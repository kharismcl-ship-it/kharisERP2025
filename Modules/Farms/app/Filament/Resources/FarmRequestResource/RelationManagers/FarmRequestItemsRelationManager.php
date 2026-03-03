<?php

namespace Modules\Farms\Filament\Resources\FarmRequestResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class FarmRequestItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Request Items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('item_id')
                ->label('Inventory Item (optional)')
                ->relationship('item', 'name')
                ->searchable()
                ->nullable()
                ->helperText('Link to a procurement inventory item, or leave blank.'),

            TextInput::make('description')
                ->required()
                ->maxLength(255),

            TextInput::make('quantity')
                ->required()
                ->numeric()
                ->step(0.001)
                ->minValue(0.001),

            TextInput::make('unit')
                ->default('unit')
                ->maxLength(50),

            TextInput::make('unit_cost')
                ->label('Unit Cost')
                ->numeric()
                ->prefix('GHS')
                ->step(0.01)
                ->nullable(),

            TextInput::make('total_cost')
                ->label('Total Cost')
                ->numeric()
                ->prefix('GHS')
                ->disabled()
                ->dehydrated()
                ->placeholder('Auto-calculated'),

            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->searchable(),
                TextColumn::make('item.name')->label('Item')->placeholder('—'),
                TextColumn::make('quantity')->numeric(decimalPlaces: 3),
                TextColumn::make('unit'),
                TextColumn::make('unit_cost')->money('GHS')->label('Unit Cost')->placeholder('—'),
                TextColumn::make('total_cost')->money('GHS')->label('Total')->placeholder('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}

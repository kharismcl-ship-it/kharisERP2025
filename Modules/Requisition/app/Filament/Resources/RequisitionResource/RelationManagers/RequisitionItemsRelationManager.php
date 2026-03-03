<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class RequisitionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('item_id')
                    ->label('Catalog Item (optional)')
                    ->relationship('procurementItem', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('description')->required()->maxLength(255),
            ]),
            Grid::make(3)->schema([
                TextInput::make('quantity')->numeric()->default(1)->required(),
                TextInput::make('unit')->default('pcs')->required(),
                TextInput::make('unit_cost')->label('Unit Cost')->numeric()->nullable(),
            ]),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->searchable()->limit(40),
                TextColumn::make('procurementItem.name')->label('Catalog Item'),
                TextColumn::make('quantity'),
                TextColumn::make('unit'),
                TextColumn::make('unit_cost')->money('GHS'),
                TextColumn::make('total_cost')->money('GHS'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}

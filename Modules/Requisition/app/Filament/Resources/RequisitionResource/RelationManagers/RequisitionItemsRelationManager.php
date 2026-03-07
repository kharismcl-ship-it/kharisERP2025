<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RequisitionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Item Details')->schema([
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
                    TextInput::make('unit_cost')->label('Unit Cost (GHS)')->numeric()->nullable(),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),

            Section::make('Vendor Quote')->schema([
                Grid::make(3)->schema([
                    TextInput::make('vendor_name')->label('Vendor Name')->nullable()->maxLength(255),
                    TextInput::make('vendor_quote_ref')->label('Quote Reference')->nullable()->maxLength(255),
                    TextInput::make('vendor_unit_price')->label('Vendor Unit Price (GHS)')->numeric()->nullable(),
                ]),
            ]),

            Section::make('Fulfilment Tracking')->schema([
                TextInput::make('fulfilled_quantity')
                    ->label('Fulfilled Quantity')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Track how much of this item has been received/fulfilled.'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->searchable()->limit(40),
                TextColumn::make('procurementItem.name')->label('Catalog Item')->placeholder('—'),
                TextColumn::make('quantity'),
                TextColumn::make('fulfilled_quantity')
                    ->label('Fulfilled')
                    ->formatStateUsing(fn ($state, $record) => $record->fulfilled_quantity . ' / ' . $record->quantity . ' (' . $record->fulfilmentPercentage() . '%)')
                    ->color(fn ($record) => $record->isFullyFulfilled() ? 'success' : 'gray'),
                TextColumn::make('unit'),
                TextColumn::make('unit_cost')->money('GHS')->placeholder('—'),
                TextColumn::make('total_cost')->money('GHS')->placeholder('—'),
                TextColumn::make('vendor_name')->label('Vendor')->placeholder('—')->toggleable(),
                TextColumn::make('vendor_unit_price')->label('Vendor Price')->money('GHS')->placeholder('—')->toggleable(),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorCatalogResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Models\Item;

class CatalogItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'catalogItems';

    protected static ?string $title = 'Catalog Items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('item_id')
                ->label('Item')
                ->options(function () {
                    $companyId = $this->ownerRecord->company_id ?? auth()->user()?->current_company_id;
                    return Item::query()
                        ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                        ->where('is_active', true)
                        ->pluck('name', 'id');
                })
                ->required()
                ->searchable(),

            Forms\Components\TextInput::make('vendor_sku')
                ->label('Vendor SKU')
                ->maxLength(100)
                ->nullable(),

            Forms\Components\TextInput::make('unit_price')
                ->numeric()
                ->required()
                ->prefix('GHS')
                ->step(0.0001),

            Forms\Components\TextInput::make('min_order_quantity')
                ->label('Min Order Qty')
                ->numeric()
                ->default(1)
                ->step(0.0001),

            Forms\Components\TextInput::make('lead_time_days')
                ->label('Lead Time (days)')
                ->numeric()
                ->nullable(),

            Forms\Components\TextInput::make('notes')
                ->maxLength(255)
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.name')->label('Item')->searchable(),
                Tables\Columns\TextColumn::make('vendor_sku')->label('Vendor SKU')->placeholder('—'),
                Tables\Columns\TextColumn::make('unit_price')->money('GHS'),
                Tables\Columns\TextColumn::make('min_order_quantity')->label('Min Qty')->numeric(4),
                Tables\Columns\TextColumn::make('lead_time_days')->label('Lead (days)')->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
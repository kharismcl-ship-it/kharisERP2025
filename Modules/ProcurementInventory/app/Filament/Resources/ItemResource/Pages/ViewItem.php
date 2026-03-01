<?php

namespace Modules\ProcurementInventory\Filament\Resources\ItemResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ProcurementInventory\Filament\Resources\ItemResource;

class ViewItem extends ViewRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Item Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('sku')->weight('bold')->label('SKU'),
                    TextEntry::make('name'),
                    TextEntry::make('category.name')->label('Category'),
                    TextEntry::make('type')
                        ->badge()
                        ->color(fn (string $state) => match ($state) {
                            'product'      => 'info',
                            'service'      => 'warning',
                            'raw_material' => 'success',
                            'asset'        => 'gray',
                            default        => 'gray',
                        }),
                    TextEntry::make('unit_of_measure')->label('Unit of Measure'),
                    TextEntry::make('unit_price')->money('GHS'),
                    TextEntry::make('company.name')->label('Company'),
                    IconEntry::make('is_active')->boolean()->label('Active'),
                ]),

            Section::make('Reorder Settings')
                ->columns(2)
                ->schema([
                    TextEntry::make('reorder_level')->label('Reorder Level'),
                    TextEntry::make('reorder_quantity')->label('Reorder Quantity'),
                ]),

            Section::make('Stock Position')
                ->columns(3)
                ->schema([
                    TextEntry::make('stockLevel.quantity_on_hand')->label('On Hand')->default('0'),
                    TextEntry::make('stockLevel.quantity_reserved')->label('Reserved')->default('0'),
                    TextEntry::make('stockLevel.quantity_on_order')->label('On Order')->default('0'),
                ]),

            Section::make('Description')
                ->collapsible()
                ->schema([
                    TextEntry::make('description')->placeholder('No description'),
                ]),

            Section::make('Audit')
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('Created'),
                    TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                ]),
        ]);
    }
}

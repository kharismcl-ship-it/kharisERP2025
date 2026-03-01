<?php

namespace Modules\ProcurementInventory\Filament\Resources\ItemCategoryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ProcurementInventory\Filament\Resources\ItemCategoryResource;

class ViewItemCategory extends ViewRecord
{
    protected static string $resource = ItemCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('slug'),
                    TextEntry::make('company.name')->label('Company'),
                    TextEntry::make('items_count')
                        ->label('Items in Category')
                        ->getStateUsing(fn ($record) => $record->items()->count()),
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

<?php

namespace Modules\Finance\Filament\Resources\InvoiceLineResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\InvoiceLineResource;

class ViewInvoiceLine extends ViewRecord
{
    protected static string $resource = InvoiceLineResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Line Item')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('invoice.invoice_number')->label('Invoice')->weight('bold'),
                        TextEntry::make('description')->columnSpanFull(),
                        TextEntry::make('quantity'),
                        TextEntry::make('unit_price')->money('GHS')->label('Unit Price'),
                        TextEntry::make('line_total')->money('GHS')->label('Line Total')->weight('bold'),
                    ]),
            ]);
    }
}

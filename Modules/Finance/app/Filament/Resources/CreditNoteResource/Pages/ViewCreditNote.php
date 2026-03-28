<?php

namespace Modules\Finance\Filament\Resources\CreditNoteResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\CreditNoteResource;

class ViewCreditNote extends ViewRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('credit_note_number')->weight('bold'),
                        TextEntry::make('status')->badge()
                            ->color(fn (string $state) => match ($state) {
                                'draft'     => 'gray',
                                'issued'    => 'info',
                                'applied'   => 'success',
                                'cancelled' => 'danger',
                                default     => 'gray',
                            }),
                        TextEntry::make('customer_name')->placeholder('—'),
                        TextEntry::make('customer_type')->placeholder('—'),
                        TextEntry::make('invoice.invoice_number')->label('Linked Invoice')->placeholder('—'),
                        TextEntry::make('issue_date')->date(),
                    ]),

                Section::make('Amounts')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('sub_total')->money('GHS'),
                        TextEntry::make('tax_total')->money('GHS'),
                        TextEntry::make('total')->money('GHS')->weight('bold'),
                    ]),

                Section::make('Notes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reason')->placeholder('—'),
                        TextEntry::make('notes')->placeholder('—'),
                    ]),
            ]);
    }
}
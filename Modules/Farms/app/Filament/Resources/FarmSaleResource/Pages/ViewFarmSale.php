<?php

namespace Modules\Farms\Filament\Resources\FarmSaleResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\FarmSaleResource;

class ViewFarmSale extends ViewRecord
{
    protected static string $resource = FarmSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_invoice')
                ->label('Create Finance Invoice')
                ->icon('heroicon-o-document-plus')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => ! $this->record->invoice_id)
                ->action(function () {
                    app(\Modules\Farms\Services\FarmService::class)->createSaleInvoice($this->record);
                    $this->refreshFormData(['invoice_id']);
                }),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Sale Overview')
                ->columns(3)
                ->schema([
                    TextEntry::make('sale_date')->date('d M Y'),

                    TextEntry::make('product_type')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'crop'      => 'success',
                            'livestock' => 'info',
                            'processed' => 'primary',
                            default     => 'gray',
                        }),

                    TextEntry::make('payment_status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'paid'    => 'success',
                            'partial' => 'warning',
                            'pending' => 'danger',
                            default   => 'gray',
                        }),

                    TextEntry::make('farm.name')->label('Farm'),
                    TextEntry::make('product_name')->label('Product'),
                    TextEntry::make('invoice_id')
                        ->label('Finance Invoice')
                        ->formatStateUsing(fn ($state) => $state ? '#' . $state : 'Not created')
                        ->color(fn ($state) => $state ? 'primary' : 'gray'),
                ]),

            Section::make('Quantity & Revenue')
                ->columns(4)
                ->schema([
                    TextEntry::make('quantity')->numeric(decimalPlaces: 3),
                    TextEntry::make('unit')->placeholder('—'),
                    TextEntry::make('unit_price')->money('GHS')->label('Unit Price'),
                    TextEntry::make('total_amount')->money('GHS')->label('Total'),
                ]),

            Section::make('Buyer')
                ->columns(2)
                ->schema([
                    TextEntry::make('buyer_name')->placeholder('—'),
                    TextEntry::make('buyer_contact')->placeholder('—'),
                ]),

            Section::make('Links')
                ->columns(2)
                ->collapsible()
                ->schema([
                    TextEntry::make('cropCycle.crop_name')->label('Crop Cycle')->placeholder('—'),
                    TextEntry::make('livestockBatch.batch_reference')->label('Livestock Batch')->placeholder('—'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Audit')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    TextEntry::make('created_at')->dateTime('d M Y H:i'),
                    TextEntry::make('updated_at')->dateTime('d M Y H:i'),
                ]),
        ]);
    }
}
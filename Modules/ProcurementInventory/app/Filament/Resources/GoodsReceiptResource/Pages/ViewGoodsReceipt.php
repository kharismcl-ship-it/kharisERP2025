<?php

namespace Modules\ProcurementInventory\Filament\Resources\GoodsReceiptResource\Pages;

use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ProcurementInventory\Filament\Resources\GoodsReceiptResource;
use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Services\ProcurementService;

class ViewGoodsReceipt extends ViewRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Goods Receipt Note')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('grn_number')->weight('bold'),
                        TextEntry::make('status')->badge()
                            ->color(fn (string $state) => $state === 'confirmed' ? 'success' : 'gray'),
                        TextEntry::make('receipt_date')->date(),
                        TextEntry::make('purchaseOrder.po_number')->label('PO Number'),
                        TextEntry::make('vendor.name')->label('Vendor'),
                        TextEntry::make('receivedBy.name')->label('Received By'),
                    ]),
                    TextEntry::make('notes')->columnSpanFull(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('confirm')
                ->label('Confirm Receipt')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'draft' && $this->record->lines()->exists())
                ->requiresConfirmation()
                ->modalDescription('Confirming this receipt will update stock levels and cannot be undone.')
                ->action(function () {
                    try {
                        $po = $this->record->purchaseOrder;
                        $lines = $this->record->lines->map(fn ($l) => [
                            'purchase_order_line_id' => $l->purchase_order_line_id,
                            'quantity_received'      => $l->quantity_received,
                        ])->toArray();

                        app(ProcurementService::class)->receiveGoods($po, $lines, $this->record->notes);

                        // The service created a new confirmed GRN — redirect back to list
                        Notification::make()->title('Goods receipt confirmed. Stock updated.')->success()->send();
                        $this->redirect(GoodsReceiptResource::getUrl('index'));
                    } catch (\Exception $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}

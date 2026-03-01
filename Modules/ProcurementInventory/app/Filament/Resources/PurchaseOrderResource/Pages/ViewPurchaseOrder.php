<?php

namespace Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Services\ProcurementService;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Purchase Order')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('po_number')->weight('bold'),
                        TextEntry::make('status')->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft'              => 'gray',
                                'submitted'          => 'warning',
                                'approved'           => 'info',
                                'ordered'            => 'primary',
                                'partially_received' => 'warning',
                                'received'           => 'success',
                                'closed'             => 'success',
                                'cancelled'          => 'danger',
                                default              => 'gray',
                            }),
                        TextEntry::make('vendor.name')->label('Vendor'),
                        TextEntry::make('po_date')->date(),
                        TextEntry::make('expected_delivery_date')->date()->label('Expected Delivery'),
                        TextEntry::make('currency'),
                        TextEntry::make('subtotal')->money('GHS'),
                        TextEntry::make('tax_total')->money('GHS'),
                        TextEntry::make('total')->money('GHS')->weight('bold'),
                    ]),
                ]),

            Section::make('Notes')
                ->schema([
                    TextEntry::make('delivery_address')->label('Delivery Address'),
                    TextEntry::make('notes'),
                    TextEntry::make('approvedBy.name')->label('Approved By')->visible(fn ($record) => $record->approved_by),
                    TextEntry::make('approved_at')->dateTime()->visible(fn ($record) => $record->approved_at),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->status === 'draft'),

            \Filament\Actions\Action::make('submit')
                ->label('Submit for Approval')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'draft')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        app(ProcurementService::class)->submit($this->record);
                        $this->refreshFormData(['status']);
                        Notification::make()->title('PO submitted for approval')->success()->send();
                    } catch (\Exception $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),

            \Filament\Actions\Action::make('approve')
                ->label('Approve PO')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'submitted')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        app(ProcurementService::class)->approve($this->record);
                        $this->refreshFormData(['status', 'approved_by', 'approved_at']);
                        Notification::make()->title('PO approved successfully')->success()->send();
                    } catch (\Exception $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),

            \Filament\Actions\Action::make('mark_ordered')
                ->label('Mark as Ordered')
                ->icon('heroicon-o-truck')
                ->color('info')
                ->visible(fn () => $this->record->status === 'approved')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        app(ProcurementService::class)->markOrdered($this->record);
                        $this->refreshFormData(['status', 'ordered_at']);
                        Notification::make()->title('PO marked as ordered')->success()->send();
                    } catch (\Exception $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),

            \Filament\Actions\Action::make('cancel')
                ->label('Cancel PO')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->canBeCancelled())
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        app(ProcurementService::class)->cancel($this->record);
                        $this->refreshFormData(['status']);
                        Notification::make()->title('PO cancelled')->warning()->send();
                    } catch (\Exception $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}
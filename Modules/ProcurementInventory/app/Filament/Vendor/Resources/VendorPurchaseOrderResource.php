<?php

namespace Modules\ProcurementInventory\Filament\Vendor\Resources;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class VendorPurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Purchase Orders';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'purchase-orders';

    public static function getEloquentQuery(): Builder
    {
        $vendorId = auth('vendor')->user()?->vendor_id;

        if (! $vendorId) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('vendor_id', $vendorId)
            ->whereNotIn('status', ['draft'])
            ->with(['lines', 'company']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('PO Number')
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Buyer'),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->label('Order Date'),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date()
                    ->label('Expected Delivery')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted'          => 'warning',
                        'approved'           => 'info',
                        'ordered'            => 'info',
                        'partially_received' => 'warning',
                        'received'           => 'success',
                        'cancelled'          => 'danger',
                        default              => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => PurchaseOrder::STATUSES[$state] ?? ucfirst($state)),

                Tables\Columns\TextColumn::make('vendor_acknowledged_at')
                    ->label('Acknowledged')
                    ->dateTime()
                    ->placeholder('Not acknowledged')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('vendor_confirmed_delivery_date')
                    ->label('Confirmed Delivery')
                    ->date()
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('acknowledge')
                    ->label('Acknowledge')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn (PurchaseOrder $record) => in_array($record->status, ['ordered', 'partially_received']) && ! $record->vendor_acknowledged_at)
                    ->requiresConfirmation()
                    ->action(function (PurchaseOrder $record) {
                        $record->update(['vendor_acknowledged_at' => now()]);
                        Notification::make()->title('PO acknowledged')->success()->send();
                    }),

                Action::make('confirm_delivery')
                    ->label('Confirm Delivery Date')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->visible(fn (PurchaseOrder $record) => in_array($record->status, ['ordered', 'partially_received']))
                    ->form([
                        Forms\Components\DatePicker::make('vendor_confirmed_delivery_date')
                            ->label('Confirmed Delivery Date')
                            ->required(),
                        Forms\Components\Textarea::make('vendor_delivery_notes')
                            ->label('Delivery Notes')
                            ->rows(3),
                    ])
                    ->action(function (PurchaseOrder $record, array $data) {
                        $record->update([
                            'vendor_confirmed_delivery_date' => $data['vendor_confirmed_delivery_date'],
                            'vendor_delivery_notes'          => $data['vendor_delivery_notes'] ?? null,
                        ]);
                        Notification::make()->title('Delivery date confirmed')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\ProcurementInventory\Filament\Vendor\Resources\Pages\ListVendorPurchaseOrders::route('/'),
        ];
    }
}

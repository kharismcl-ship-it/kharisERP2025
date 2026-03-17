<?php

namespace Modules\ProcurementInventory\Filament\Vendor\Resources;

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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

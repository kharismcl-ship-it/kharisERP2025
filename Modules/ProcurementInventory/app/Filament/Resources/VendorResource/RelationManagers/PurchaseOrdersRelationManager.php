<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class PurchaseOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseOrders';

    protected static ?string $title = 'Purchase Orders';

    public function form(Schema $schema): Schema
    {
        // POs are created and managed via PurchaseOrderResource
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('po_number')->searchable()->weight('bold'),
                TextColumn::make('po_date')->date()->sortable(),
                TextColumn::make('expected_delivery_date')->date()->label('Exp. Delivery')->toggleable(),
                TextColumn::make('status')->badge()
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
                TextColumn::make('total')->money('GHS')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PurchaseOrder::STATUSES),
            ])
            ->headerActions([
                Tables\Actions\Action::make('new_po')
                    ->label('New Purchase Order')
                    ->icon('heroicon-o-plus')
                    ->url(fn () => PurchaseOrderResource::getUrl('create')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => PurchaseOrderResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([])
            ->defaultSort('po_date', 'desc');
    }
}

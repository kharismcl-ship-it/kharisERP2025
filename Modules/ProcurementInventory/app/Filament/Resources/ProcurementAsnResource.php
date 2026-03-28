<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\ProcurementAsnResource\Pages;
use Modules\ProcurementInventory\Models\ProcurementAsn;

class ProcurementAsnResource extends Resource
{
    protected static ?string $model = ProcurementAsn::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 44;

    protected static ?string $label = 'Shipment Notice';

    protected static ?string $pluralLabel = 'Shipment Notices';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('asn_number')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('purchaseOrder.po_number')
                    ->label('PO Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->label('Expected Delivery')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('carrier_name')
                    ->label('Carrier')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Tracking')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'submitted'    => 'warning',
                        'acknowledged' => 'info',
                        'received'     => 'success',
                        default        => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted'    => 'Submitted',
                        'acknowledged' => 'Acknowledged',
                        'received'     => 'Received',
                    ]),
            ])
            ->actions([
                Action::make('acknowledge')
                    ->label('Acknowledge')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn (ProcurementAsn $record) => $record->status === 'submitted')
                    ->requiresConfirmation()
                    ->action(function (ProcurementAsn $record) {
                        $record->update(['status' => 'acknowledged']);
                        Notification::make()->title('ASN acknowledged')->success()->send();
                    }),

                Action::make('mark_received')
                    ->label('Mark Received')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('success')
                    ->visible(fn (ProcurementAsn $record) => $record->status === 'acknowledged')
                    ->requiresConfirmation()
                    ->action(function (ProcurementAsn $record) {
                        $record->update(['status' => 'received']);
                        Notification::make()->title('ASN marked as received')->success()->send();
                    }),

                ViewAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcurementAsns::route('/'),
            'view'  => Pages\ViewProcurementAsn::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // ASNs are submitted by vendors
    }
}
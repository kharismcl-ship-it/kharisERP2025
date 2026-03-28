<?php

namespace Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Models\ProcurementAsn;

class ProcurementAsnRelationManager extends RelationManager
{
    protected static string $relationship = 'asns';

    protected static ?string $title = 'Shipment Notices (ASN)';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('asn_number')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->label('Expected Delivery')
                    ->date(),

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
            ->headerActions([])
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
            ])
            ->bulkActions([]);
    }
}
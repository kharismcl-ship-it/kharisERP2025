<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\ProcurementInvoiceMatchResource\Pages;
use Modules\ProcurementInventory\Models\ProcurementInvoiceMatch;

class ProcurementInvoiceMatchResource extends Resource
{
    protected static ?string $model = ProcurementInvoiceMatch::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = '3-Way Match';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('purchaseOrder.po_number')
                    ->label('PO Number')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('goodsReceipt.grn_number')
                    ->label('GRN Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('po_total')
                    ->money('GHS')
                    ->label('PO Total'),

                Tables\Columns\TextColumn::make('grn_total')
                    ->money('GHS')
                    ->label('GRN Total'),

                Tables\Columns\TextColumn::make('invoice_total')
                    ->money('GHS')
                    ->label('Invoice Total')
                    ->placeholder('Pending'),

                Tables\Columns\TextColumn::make('po_grn_variance')
                    ->money('GHS')
                    ->label('PO/GRN Variance')
                    ->color(fn ($record) => (float) $record->po_grn_variance !== 0.0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'matched'              => 'success',
                        'po_grn_mismatch'      => 'danger',
                        'grn_invoice_mismatch' => 'danger',
                        'pending_invoice'      => 'warning',
                        default                => 'gray',
                    }),

                Tables\Columns\TextColumn::make('matched_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'matched'              => 'Matched',
                        'po_grn_mismatch'      => 'PO/GRN Mismatch',
                        'grn_invoice_mismatch' => 'GRN/Invoice Mismatch',
                        'pending_invoice'      => 'Pending Invoice',
                    ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcurementInvoiceMatches::route('/'),
        ];
    }
}
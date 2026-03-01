<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\GoodsReceiptResource\Pages;
use Modules\ProcurementInventory\Models\GoodsReceipt;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Services\ProcurementService;

class GoodsReceiptResource extends Resource
{
    protected static ?string $model = GoodsReceipt::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Receipt Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('purchase_order_id')
                        ->label('Purchase Order')
                        ->options(
                            PurchaseOrder::whereIn('status', ['ordered', 'partially_received'])
                                ->get()
                                ->mapWithKeys(fn ($po) => [$po->id => "{$po->po_number} — {$po->vendor->name}"])
                        )
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $po = PurchaseOrder::find($state);
                                if ($po) {
                                    $set('vendor_id', $po->vendor_id);
                                }
                            }
                        }),

                    Forms\Components\Select::make('vendor_id')
                        ->relationship('vendor', 'name')
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('grn_number')
                        ->disabled()
                        ->placeholder('Auto-generated'),

                    Forms\Components\DatePicker::make('receipt_date')
                        ->default(now())
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->options(['draft' => 'Draft', 'confirmed' => 'Confirmed'])
                        ->default('draft')
                        ->disabled(),
                ]),

            Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('grn_number')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('purchaseOrder.po_number')
                    ->label('PO Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('receipt_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('receivedBy.name')
                    ->label('Received By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'confirmed' => 'Confirmed']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn (GoodsReceipt $record) => $record->status === 'draft'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGoodsReceipts::route('/'),
            'create' => Pages\CreateGoodsReceipt::route('/create'),
            'view'   => Pages\ViewGoodsReceipt::route('/{record}'),
        ];
    }
}

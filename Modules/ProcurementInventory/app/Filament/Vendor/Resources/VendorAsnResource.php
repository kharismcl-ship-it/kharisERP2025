<?php

namespace Modules\ProcurementInventory\Filament\Vendor\Resources;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\ProcurementInventory\Models\ProcurementAsn;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class VendorAsnResource extends Resource
{
    protected static ?string $model = ProcurementAsn::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Shipment Notices';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'shipment-notices';

    public static function getEloquentQuery(): Builder
    {
        $vendorId = auth('vendor')->user()?->vendor_id;

        if (! $vendorId) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('vendor_id', $vendorId)
            ->with(['purchaseOrder', 'lines']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Shipment Notice')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('purchase_order_id')
                        ->label('Purchase Order')
                        ->options(function () {
                            $vendorId = auth('vendor')->user()?->vendor_id;
                            if (! $vendorId) return [];
                            return PurchaseOrder::where('vendor_id', $vendorId)
                                ->whereIn('status', ['approved', 'ordered', 'partially_received'])
                                ->pluck('po_number', 'id')
                                ->toArray();
                        })
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('asn_number')
                        ->label('Your Shipment Reference')
                        ->required(),

                    Forms\Components\DatePicker::make('expected_delivery_date')
                        ->label('Expected Delivery Date')
                        ->required(),

                    Forms\Components\TextInput::make('carrier_name')
                        ->label('Carrier'),

                    Forms\Components\TextInput::make('tracking_number')
                        ->label('Tracking Number'),

                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Line Items')
                ->schema([
                    Forms\Components\Repeater::make('lines')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('purchase_order_line_id')
                                ->label('PO Line')
                                ->options(function (callable $get) {
                                    $poId = $get('../../purchase_order_id');
                                    if (! $poId) return [];
                                    return \Modules\ProcurementInventory\Models\PurchaseOrderLine::where('purchase_order_id', $poId)
                                        ->with('item')
                                        ->get()
                                        ->mapWithKeys(fn ($l) => [$l->id => ($l->item?->name ?? 'Item') . ' — Qty: ' . $l->quantity]);
                                })
                                ->required(),

                            Forms\Components\TextInput::make('quantity_shipped')
                                ->label('Qty Shipped')
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('lot_number')
                                ->label('Lot Number'),
                        ])
                        ->columns(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('asn_number')
                    ->label('Shipment Ref')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('purchaseOrder.po_number')
                    ->label('PO Number'),

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

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\ProcurementInventory\Filament\Vendor\Resources\Pages\ListVendorAsns::route('/'),
            'create' => \Modules\ProcurementInventory\Filament\Vendor\Resources\Pages\CreateVendorAsn::route('/create'),
        ];
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $vendorId = auth('vendor')->user()?->vendor_id;
        $data['vendor_id']    = $vendorId;
        $data['status']       = 'submitted';
        $data['submitted_at'] = now();

        // Set company_id from PO
        $po = PurchaseOrder::find($data['purchase_order_id']);
        $data['company_id'] = $po?->company_id;

        return $data;
    }
}
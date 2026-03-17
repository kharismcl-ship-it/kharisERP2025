<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Actions\DispatchOrderAction;
use Modules\Farms\Filament\Clusters\FarmMarketplaceCluster;
use Modules\Farms\Filament\Resources\FarmOrderResource\Pages;
use Modules\Farms\Models\FarmOrder;

class FarmOrderResource extends Resource
{
    protected static ?string $model = FarmOrder::class;

    protected static ?string $cluster = FarmMarketplaceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Status')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'pending'    => 'Pending',
                            'confirmed'  => 'Confirmed',
                            'processing' => 'Processing',
                            'ready'      => 'Ready',
                            'delivered'  => 'Delivered',
                            'cancelled'  => 'Cancelled',
                        ])
                        ->required(),
                    Select::make('payment_status')
                        ->options([
                            'pending'  => 'Pending',
                            'paid'     => 'Paid',
                            'failed'   => 'Failed',
                            'refunded' => 'Refunded',
                        ])
                        ->required(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('ref')->badge()->color('success'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'pending'    => 'warning',
                            'confirmed'  => 'info',
                            'processing' => 'primary',
                            'ready'      => 'teal',
                            'delivered'  => 'success',
                            'cancelled'  => 'danger',
                            default      => 'gray',
                        }),
                    TextEntry::make('payment_status')
                        ->label('Payment')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'paid'     => 'success',
                            'pending'  => 'warning',
                            'failed'   => 'danger',
                            'refunded' => 'gray',
                            default    => 'gray',
                        }),
                    TextEntry::make('placed_at')->dateTime()->label('Placed'),
                    TextEntry::make('preferred_delivery_date')->date()->label('Delivery Date')->placeholder('—'),
                ]),

            Section::make('Customer')
                ->columns(3)
                ->schema([
                    TextEntry::make('customer_name'),
                    TextEntry::make('customer_phone'),
                    TextEntry::make('customer_email')->placeholder('—'),
                    TextEntry::make('delivery_type')
                        ->formatStateUsing(fn ($state) => $state === 'pickup' ? '🚶 Pickup' : '🚚 Delivery'),
                    TextEntry::make('delivery_address')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('delivery_landmark')->placeholder('—')->label('Nearest Landmark'),
                ]),

            Section::make('Financials')
                ->columns(3)
                ->schema([
                    TextEntry::make('subtotal')->money('GHS'),
                    TextEntry::make('delivery_fee')->money('GHS'),
                    TextEntry::make('discount_amount')->money('GHS')->label('Discount')->placeholder('—'),
                    TextEntry::make('coupon_code')->badge()->color('success')->label('Coupon')->placeholder('—'),
                    TextEntry::make('total')->money('GHS')->weight('bold'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ref')
                    ->label('Order Ref')
                    ->searchable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('customer_name')->searchable(),
                TextColumn::make('customer_phone'),

                TextColumn::make('delivery_type')
                    ->formatStateUsing(fn ($state) => $state === 'pickup' ? '🚶 Pickup' : '🚚 Delivery'),

                TextColumn::make('total')->money('GHS')->label('Total')->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'    => 'warning',
                        'confirmed'  => 'info',
                        'processing' => 'primary',
                        'ready'      => 'teal',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'paid'     => 'success',
                        'pending'  => 'warning',
                        'failed'   => 'danger',
                        'refunded' => 'gray',
                        default    => 'gray',
                    }),

                TextColumn::make('placed_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending'    => 'Pending',
                    'confirmed'  => 'Confirmed',
                    'processing' => 'Processing',
                    'ready'      => 'Ready',
                    'delivered'  => 'Delivered',
                    'cancelled'  => 'Cancelled',
                ]),
                SelectFilter::make('payment_status')->options([
                    'pending'  => 'Pending',
                    'paid'     => 'Paid',
                    'failed'   => 'Failed',
                    'refunded' => 'Refunded',
                ]),
            ])
            ->actions([
                ViewAction::make(),
                DispatchOrderAction::make(),
                EditAction::make()->label('Update Status'),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('placed_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmOrders::route('/'),
            'view'   => Pages\ViewFarmOrder::route('/{record}'),
            'edit'   => Pages\EditFarmOrder::route('/{record}/edit'),
        ];
    }
}

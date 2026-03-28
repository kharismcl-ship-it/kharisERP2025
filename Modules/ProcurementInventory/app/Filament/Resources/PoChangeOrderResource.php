<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\PoChangeOrderResource\Pages;
use Modules\ProcurementInventory\Models\PoChangeOrder;

class PoChangeOrderResource extends Resource
{
    protected static ?string $model = PoChangeOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 43;

    protected static ?string $label = 'Change Order';

    protected static ?string $pluralLabel = 'Change Orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('purchase_order_id')
                        ->relationship('purchaseOrder', 'po_number')
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('change_type')
                        ->options([
                            'price_change'          => 'Price Change',
                            'quantity_change'       => 'Quantity Change',
                            'delivery_date_change'  => 'Delivery Date Change',
                            'vendor_change'         => 'Vendor Change',
                            'cancellation'          => 'Cancellation',
                            'other'                 => 'Other',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('previous_total')
                        ->label('Previous Total')
                        ->numeric()
                        ->prefix('GHS')
                        ->required(),

                    Forms\Components\TextInput::make('new_total')
                        ->label('New Total')
                        ->numeric()
                        ->prefix('GHS'),

                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('change_order_number')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('purchaseOrder.po_number')
                    ->label('PO Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('change_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'price_change'         => 'Price',
                        'quantity_change'      => 'Quantity',
                        'delivery_date_change' => 'Delivery Date',
                        'vendor_change'        => 'Vendor',
                        'cancellation'         => 'Cancellation',
                        'other'                => 'Other',
                        default                => $state,
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('amount_change')
                    ->label('Change Amount')
                    ->money('GHS')
                    ->color(fn (PoChangeOrder $record): string => (float) $record->amount_change > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'            => 'gray',
                        'pending_approval' => 'warning',
                        'approved'         => 'success',
                        'rejected'         => 'danger',
                        default            => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'            => 'Draft',
                        'pending_approval' => 'Pending Approval',
                        'approved'         => 'Approved',
                        'rejected'         => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PoChangeOrder $record) => $record->status === 'pending_approval')
                    ->requiresConfirmation()
                    ->action(function (PoChangeOrder $record) {
                        $record->update([
                            'status'               => 'approved',
                            'approved_by_user_id'  => auth()->id(),
                            'approved_at'          => now(),
                        ]);
                        Notification::make()->title('Change order approved')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PoChangeOrder $record) => $record->status === 'pending_approval')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (PoChangeOrder $record, array $data) {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()->title('Change order rejected')->warning()->send();
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
            'index' => Pages\ListPoChangeOrders::route('/'),
            'view'  => Pages\ViewPoChangeOrder::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Created via PO actions
    }
}
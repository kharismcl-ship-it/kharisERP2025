<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\WarehouseTransferResource\Pages;
use Modules\ProcurementInventory\Models\Item;
use Modules\ProcurementInventory\Models\Warehouse;
use Modules\ProcurementInventory\Models\WarehouseTransfer;
use Modules\ProcurementInventory\Services\WarehouseTransferService;

class WarehouseTransferResource extends Resource
{
    protected static ?string $model = WarehouseTransfer::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Transfer Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        
                        ->searchable(),

                    Forms\Components\TextInput::make('reference')
                        ->label('Reference')
                        ->placeholder('Auto-generated')
                        ->maxLength(50),

                    Forms\Components\Select::make('from_warehouse_id')
                        ->label('From Warehouse')
                        ->options(fn () => Warehouse::active()->pluck('name', 'id'))
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('to_warehouse_id')
                        ->label('To Warehouse')
                        ->options(fn () => Warehouse::active()->pluck('name', 'id'))
                        ->required()
                        ->searchable(),

                    Forms\Components\Textarea::make('notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Items to Transfer')
                ->schema([
                    Forms\Components\Repeater::make('lines')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('item_id')
                                ->label('Item')
                                ->options(fn () => Item::active()->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('quantity_requested')
                                ->label('Quantity')
                                ->required()
                                ->numeric()
                                ->minValue(0.0001),

                            Forms\Components\TextInput::make('notes')
                                ->label('Notes')
                                ->maxLength(255),
                        ])
                        ->columns(4)
                        ->minItems(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('fromWarehouse.name')
                    ->label('From'),

                Tables\Columns\TextColumn::make('toWarehouse.name')
                    ->label('To'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'       => 'gray',
                        'in_transit'  => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('lines_count')
                    ->counts('lines')
                    ->label('Lines'),

                Tables\Columns\TextColumn::make('requestedBy.name')
                    ->label('Requested By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('transferred_at')
                    ->label('Dispatched')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'      => 'Draft',
                        'in_transit' => 'In Transit',
                        'completed'  => 'Completed',
                        'cancelled'  => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('from_warehouse')
                    ->relationship('fromWarehouse', 'name'),
                Tables\Filters\SelectFilter::make('to_warehouse')
                    ->relationship('toWarehouse', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (WarehouseTransfer $record) => $record->isDraft()),

                Action::make('dispatch')
                    ->label('Dispatch')
                    ->icon(Heroicon::OutlinedTruck)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (WarehouseTransfer $record) => $record->isDraft())
                    ->action(function (WarehouseTransfer $record): void {
                        try {
                            app(WarehouseTransferService::class)->dispatchTransfer($record);
                            Notification::make()->success()->title('Transfer dispatched')->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Error')->body($e->getMessage())->send();
                        }
                    }),

                Action::make('complete')
                    ->label('Complete')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (WarehouseTransfer $record) => $record->isInTransit() || $record->isDraft())
                    ->action(function (WarehouseTransfer $record): void {
                        try {
                            app(WarehouseTransferService::class)->completeTransfer($record);
                            Notification::make()->success()->title('Transfer completed — stock moved')->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Insufficient stock')->body($e->getMessage())->send();
                        }
                    }),

                Action::make('cancel')
                    ->label('Cancel')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (WarehouseTransfer $record) => ! $record->isCompleted() && ! $record->isCancelled())
                    ->action(function (WarehouseTransfer $record): void {
                        try {
                            app(WarehouseTransferService::class)->cancelTransfer($record);
                            Notification::make()->success()->title('Transfer cancelled')->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Error')->body($e->getMessage())->send();
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWarehouseTransfers::route('/'),
            'create' => Pages\CreateWarehouseTransfer::route('/create'),
            'view'   => Pages\ViewWarehouseTransfer::route('/{record}'),
            'edit'   => Pages\EditWarehouseTransfer::route('/{record}/edit'),
        ];
    }
}
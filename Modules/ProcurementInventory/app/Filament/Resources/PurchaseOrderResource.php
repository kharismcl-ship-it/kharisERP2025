<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource\Pages;
use Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource\RelationManagers;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Services\ProcurementService;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Order Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('vendor_id')
                        ->relationship('vendor', 'name', fn ($query) => $query->where('status', 'active'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\TextInput::make('po_number')
                        ->disabled()
                        ->placeholder('Auto-generated'),

                    Forms\Components\Select::make('status')
                        ->options(PurchaseOrder::STATUSES)
                        ->default('draft')
                        ->disabled()
                        ->required(),

                    Forms\Components\DatePicker::make('po_date')
                        ->default(now())
                        ->required(),

                    Forms\Components\DatePicker::make('expected_delivery_date'),

                    Forms\Components\TextInput::make('currency')
                        ->default('GHS')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('payment_terms')
                        ->numeric()
                        ->suffix('days')
                        ->placeholder('Uses vendor default'),
                ]),

            Forms\Components\Section::make('Delivery & Notes')
                ->columns(1)
                ->schema([
                    Forms\Components\Textarea::make('delivery_address')->rows(2),
                    Forms\Components\Textarea::make('notes')->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('po_number')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('po_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
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

                Tables\Columns\TextColumn::make('total')
                    ->money('GHS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(PurchaseOrder::STATUSES),
            ])
            ->actions([
                Tables\Actions\Action::make('submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (PurchaseOrder $record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(function (PurchaseOrder $record) {
                        try {
                            app(ProcurementService::class)->submit($record);
                            Notification::make()->title('PO submitted for approval')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PurchaseOrder $record) => $record->status === 'submitted')
                    ->requiresConfirmation()
                    ->action(function (PurchaseOrder $record) {
                        try {
                            app(ProcurementService::class)->approve($record);
                            Notification::make()->title('PO approved')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('mark_ordered')
                    ->label('Mark Ordered')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (PurchaseOrder $record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (PurchaseOrder $record) {
                        try {
                            app(ProcurementService::class)->markOrdered($record);
                            Notification::make()->title('PO marked as ordered')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (PurchaseOrder $record) => $record->status === 'draft'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PurchaseOrderLinesRelationManager::class,
            RelationManagers\GoodsReceiptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit'   => Pages\EditPurchaseOrder::route('/{record}/edit'),
            'view'   => Pages\ViewPurchaseOrder::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['po_number'];
    }
}

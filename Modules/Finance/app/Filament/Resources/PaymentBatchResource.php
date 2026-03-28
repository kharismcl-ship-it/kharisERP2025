<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\PaymentBatchResource\Pages;
use Modules\Finance\Filament\Resources\PaymentBatchResource\RelationManagers\PaymentBatchItemsRelationManager;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\PaymentBatch;

class PaymentBatchResource extends Resource
{
    protected static ?string $model = PaymentBatch::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 22;

    protected static ?string $navigationLabel = 'Payment Batches';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Batch Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('batch_number')
                            ->disabled()
                            ->placeholder('Auto-generated'),
                        Forms\Components\DatePicker::make('batch_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'bank'   => 'Bank Transfer',
                                'cheque' => 'Cheque',
                                'momo'   => 'Mobile Money',
                            ])
                            ->required(),
                        Forms\Components\Select::make('bank_account_id')
                            ->relationship('bankAccount', 'name')
                            ->searchable()
                            ->label('Bank Account'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'approved'  => 'Approved',
                                'processed' => 'Processed',
                            ])
                            ->default('draft'),
                        Forms\Components\Textarea::make('notes')->columnSpanFull()->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('batch_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('total_amount')->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'     => 'gray',
                        'approved'  => 'info',
                        'processed' => 'success',
                        default     => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('process_batch')
                    ->label('Process Batch')
                    ->icon(Heroicon::OutlinedBolt)
                    ->color('success')
                    ->visible(fn (PaymentBatch $record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (PaymentBatch $record) {
                        foreach ($record->items as $item) {
                            Payment::create([
                                'company_id'     => $record->company_id,
                                'invoice_id'     => $item->invoice_id,
                                'amount'         => $item->amount,
                                'payment_date'   => $record->batch_date,
                                'payment_method' => $record->payment_method,
                                'reference'      => $item->reference ?? $record->batch_number,
                            ]);
                        }
                        $record->update(['status' => 'processed']);
                    }),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PaymentBatchItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPaymentBatches::route('/'),
            'create' => Pages\CreatePaymentBatch::route('/create'),
            'view'   => Pages\ViewPaymentBatch::route('/{record}'),
            'edit'   => Pages\EditPaymentBatch::route('/{record}/edit'),
        ];
    }
}
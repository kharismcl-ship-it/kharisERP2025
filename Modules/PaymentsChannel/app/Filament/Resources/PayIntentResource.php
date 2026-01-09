<?php

namespace Modules\PaymentsChannel\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\PaymentsChannel\Events\PaymentSucceeded;
use Modules\PaymentsChannel\Filament\Resources\PayIntentResource\Pages;
use Modules\PaymentsChannel\Models\PayIntent;
use Modules\PaymentsChannel\Models\PayTransaction;

class PayIntentResource extends Resource
{
    protected static ?string $model = PayIntent::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|\UnitEnum|null $navigationGroup = 'Payments';

    protected static string $title = 'Payment Intents';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                Forms\Components\Select::make('provider')
                    ->options([
                        'flutterwave' => 'Flutterwave',
                        'paystack' => 'Paystack',
                        'payswitch' => 'PaySwitch',
                        'stripe' => 'Stripe',
                        'ghanapay' => 'GhanaPay',
                        'manual' => 'Manual',
                    ])
                    ->required(),
                Forms\Components\Select::make('pay_method_id')
                    ->relationship('payMethod', 'name')
                    ->nullable(),
                Forms\Components\TextInput::make('payable_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payable_id')
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('provider_reference')
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'pending_offline' => 'Pending Offline',
                        'initiated' => 'Initiated',
                        'processing' => 'Processing',
                        'successful' => 'Successful',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('customer_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('return_url')
                    ->maxLength(255),
                Forms\Components\TextInput::make('callback_url')
                    ->maxLength(255),
                Forms\Components\KeyValue::make('metadata'),
                Forms\Components\DateTimePicker::make('expires_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->label('Customer Name')
                    ->weight('bold')
                    ->description(fn (PayIntent $record) => $record->metadata['booking_reference'] ?? null),
                Tables\Columns\TextColumn::make('metadata.booking_reference')
                    ->searchable()
                    ->label('Booking Reference')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description(fn (PayIntent $record) => $record->metadata['booking_reference'] ?? null),

                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->label('Company')
                    ->weight('bold')
                    ->description(fn (PayIntent $record) => $record->payable_type ?? null),
                Tables\Columns\TextColumn::make('provider')
                    ->searchable()
                    ->label('Provider')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payMethod.name')
                    ->searchable()
                    ->label('Payment Method')
                    ->weight('bold')
                    ->description(fn (PayIntent $record) => $record->provider ?? null),
                Tables\Columns\TextColumn::make('payable_type')
                    ->searchable()
                    ->label('Payable Type')
                    ->weight('bold')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->label('Reference')
                    ->weight('bold')
                    ->description(fn (PayIntent $record) => $record->metadata['reference'] ?? null),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->money('ghs')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'pending_offline' => 'warning',
                        'initiated' => 'info',
                        'processing' => 'info',
                        'successful' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'danger',
                        'expired' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider'),
                Tables\Filters\SelectFilter::make('status'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    \Filament\Actions\Action::make('confirm_manual_payment')
                        ->label('Confirm Payment')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(function (PayIntent $record) {
                            return ($record->provider === 'manual' || $record->status === 'pending_offline') &&
                                   ($record->status === 'pending' || $record->status === 'pending_offline');
                        })
                        ->form([
                                Forms\Components\TextInput::make('transaction_id')
                                    ->label('Transaction ID/Reference')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Confirmation Notes')
                                    ->maxLength(500),
                            ])
                        ->action(function (PayIntent $record, array $data) {
                            // Update intent status
                            $record->update([
                                'status' => 'successful',
                                'provider_reference' => $data['transaction_id'],
                            ]);

                            // Create transaction record
                            PayTransaction::create([
                                'pay_intent_id' => $record->id,
                                'company_id' => $record->company_id,
                                'provider' => 'manual',
                                'transaction_type' => 'payment',
                                'amount' => $record->amount,
                                'currency' => $record->currency,
                                'provider_transaction_id' => $data['transaction_id'],
                                'status' => 'successful',
                                'raw_payload' => [
                                    'confirmed_by' => \Illuminate\Support\Facades\Auth::id(),
                                    'notes' => $data['notes'] ?? null,
                                ],
                                'processed_at' => now(),
                            ]);

                            // Fire success event
                            event(new PaymentSucceeded($record));

                            // Update related booking if it's a hostel booking
                            $payable = $record->payable;
                            if ($payable && method_exists($payable, 'update')) {
                                // If it's a hostel booking, update its status
                                if (get_class($payable) === 'Modules\Hostels\Models\Booking') {
                                    $payable->update(['status' => 'confirmed']);

                                    // Update bed status if it exists
                                    if ($payable->bed_id && $bed = $payable->bed) {
                                        $bed->update(['status' => 'occupied']);
                                    }
                                }
                            }
                        }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayIntents::route('/'),
            'create' => Pages\CreatePayIntent::route('/create'),
            'edit' => Pages\EditPayIntent::route('/{record}/edit'),
            'view' => Pages\ViewPayIntent::route('/{record}'),
        ];
    }
}

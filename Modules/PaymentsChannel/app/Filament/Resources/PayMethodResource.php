<?php

namespace Modules\PaymentsChannel\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\PaymentsChannel\Filament\Resources\PayMethodResource\Pages;
use Modules\PaymentsChannel\Models\PayMethod;

class PayMethodResource extends Resource
{
    protected static ?string $model = PayMethod::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|\UnitEnum|null $navigationGroup = 'Payments';

    protected static string $title = 'Payment Methods';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Company Details')
                    ->components([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->live()
                            ->required(),

                        Forms\Components\Select::make('provider')
                            ->options(function (callable $get) {
                                $companyId = $get('company_id');

                                if (! $companyId) {
                                    // If no company selected, show all available gateways
                                    $discoveryService = new \Modules\PaymentsChannel\Services\GatewayDiscoveryService;

                                    return $discoveryService->getAvailableGateways();
                                }

                                // Get company-specific enabled providers
                                $enabledProviders = \Modules\PaymentsChannel\Models\PayProviderConfig::where('company_id', $companyId)
                                    ->where('is_active', true)
                                    ->pluck('provider')
                                    ->toArray();

                                $discoveryService = new \Modules\PaymentsChannel\Services\GatewayDiscoveryService;
                                $allGateways = $discoveryService->getAvailableGateways();

                                // Filter to only show providers configured for this company
                                return array_filter($allGateways, function ($key) use ($enabledProviders) {
                                    return in_array($key, $enabledProviders);
                                }, ARRAY_FILTER_USE_KEY);
                            })
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('channel')
                            ->options([
                                'card' => 'Card',
                                'momo' => 'Mobile Money',
                                'bank' => 'Bank Transfer',
                                'wallet' => 'Wallet',
                            ])
                            ->reactive()
                            ->required(),

                        Forms\Components\Select::make('payment_mode')
                            ->options([
                                'online' => 'Online Payment',
                                'offline' => 'Offline Payment',
                            ])
                            ->default('online')
                            ->required()
                            ->live()
                            ->helperText('Online: Processed through payment gateway. Offline: Manual/cash/bank transfer.'),

                        Forms\Components\TextInput::make('currency')
                            ->maxLength(255),

                    ]),

                Section::make('Payment Method Details')
                    ->columns(3)
                    ->components([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('code')
                            ->maxLength(255)
                            ->disabled(fn (callable $get) => $get('channel'))
                            ->dehydrated()
                            ->helperText('Auto-generated based on channel. Edit only if needed.'),

                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),

                    ]),

                Section::make('Offline Payment Instructions')
                    ->description('These instructions will be shown to users when they select offline payment methods')
                    ->collapsible()
                    ->columnSpanFull()
                    ->collapsed(fn (callable $get) => $get('payment_mode') !== 'offline')
                    ->components([
                        Forms\Components\RichEditor::make('offline_payment_instruction')
                            ->label('Payment Instructions')
                            ->placeholder('Enter clear instructions for users to complete offline payments. For example:\n\nBank Name: Standard Chartered Bank\nAccount Number: 1234567890\nAccount Name: Kharis ERP Hostels\nReference: BOOKING-{reference}')
                            ->maxLength(1000)
                            ->helperText('This text will be displayed to users when they select this offline payment method.')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (callable $get) => $get('payment_mode') === 'offline'),

                Forms\Components\KeyValue::make('config')
                    ->keyLabel('Configuration Key')
                    ->valueLabel('Configuration Value')
                    ->reorderable()
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_mode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provider')
                    ->searchable(),
                Tables\Columns\TextColumn::make('channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
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
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
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
            'index' => Pages\ListPayMethods::route('/'),
            'create' => Pages\CreatePayMethod::route('/create'),
            'edit' => Pages\EditPayMethod::route('/{record}/edit'),
        ];
    }
}

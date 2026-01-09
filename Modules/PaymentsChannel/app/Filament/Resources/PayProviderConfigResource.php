<?php

namespace Modules\PaymentsChannel\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\PaymentsChannel\Filament\Resources\PayProviderConfigResource\Pages;
use Modules\PaymentsChannel\Models\PayProviderConfig;

class PayProviderConfigResource extends Resource
{
    protected static ?string $model = PayProviderConfig::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    protected static string|\UnitEnum|null $navigationGroup = 'Payments';

    protected static ?string $modelLabel = 'Provider Configurations';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->nullable(),
                Forms\Components\Select::make('provider')
                    ->options(function () {
                        $discoveryService = new \Modules\PaymentsChannel\Services\GatewayDiscoveryService;

                        return $discoveryService->getAvailableGateways();
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Auto-set name when provider changes
                        if ($state) {
                            $discoveryService = new \Modules\PaymentsChannel\Services\GatewayDiscoveryService;
                            $gateways = $discoveryService->getAvailableGateways();
                            $set('name', $gateways[$state].' Service Provider');
                        }
                    }),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_default')
                    ->required()
                    ->reactive(),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('mode')
                    ->options([
                        'sandbox' => 'Sandbox',
                        'live' => 'Live',
                    ])
                    ->default('sandbox')
                    ->required(),
                Forms\Components\KeyValue::make('config')
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
                Tables\Columns\TextColumn::make('provider')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('mode')
                    ->badge()
                    ->searchable(),
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
            'index' => Pages\ListPayProviderConfigs::route('/'),
            'create' => Pages\CreatePayProviderConfig::route('/create'),
            'edit' => Pages\EditPayProviderConfig::route('/{record}/edit'),
        ];
    }
}

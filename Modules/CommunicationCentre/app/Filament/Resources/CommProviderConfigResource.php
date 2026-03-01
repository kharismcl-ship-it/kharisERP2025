<?php

namespace Modules\CommunicationCentre\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\CommunicationCentre\Filament\Resources\CommProviderConfigResource\Pages;
use Modules\CommunicationCentre\Models\CommProviderConfig;

class CommProviderConfigResource extends Resource
{
    protected static ?string $model = CommProviderConfig::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->nullable(),
                Forms\Components\Select::make('channel')
                    ->options(function () {
                        $channels = config('communicationcentre.channels', []);
                        $channelLabels = [
                            'email' => 'Email',
                            'sms' => 'SMS',
                            'whatsapp' => 'WhatsApp',
                            'database' => 'Database',
                        ];

                        $options = [];
                        foreach ($channels as $channel) {
                            $options[$channel] = $channelLabels[$channel] ?? ucfirst($channel);
                        }

                        return $options;
                    })
                    ->required(),
                Forms\Components\Select::make('provider')
                    ->options(function () {
                        $providers = config('communicationcentre.providers', []);
                        $providerLabels = [
                            'laravel_mail' => 'Laravel Mail',
                            'mailtrap' => 'Mailtrap',
                            'twilio' => 'Twilio',
                            'mnotify' => 'mNotify',
                            'wasender' => 'Wasender',
                            'filament_database' => 'Filament Database',
                        ];

                        $options = [];
                        foreach ($providers as $provider) {
                            $options[$provider] = $providerLabels[$provider] ?? ucfirst(str_replace('_', ' ', $provider));
                        }

                        return $options;
                    })
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_default')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provider')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                EditAction::make(),
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
            'index' => Pages\ListCommProviderConfigs::route('/'),
            'create' => Pages\CreateCommProviderConfig::route('/create'),
            'edit' => Pages\EditCommProviderConfig::route('/{record}/edit'),
        ];
    }
}

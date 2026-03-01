<?php

namespace Modules\CommunicationCentre\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\CommunicationCentre\Filament\Resources\CommTemplateResource\Pages;
use Modules\CommunicationCentre\Models\CommProviderConfig;
use Modules\CommunicationCentre\Models\CommTemplate;

class CommTemplateResource extends Resource
{
    protected static ?string $model = CommTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->live()
                    ->nullable(),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
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
                    ->required()
                    ->live(),
                Forms\Components\Select::make('provider')
                    ->options(function (Get $get) {
                        $channel = $get('channel');
                        $companyId = $get('company_id');

                        if (! $channel) {
                            return [];
                        }

                        $query = CommProviderConfig::where('channel', $channel)
                            ->where('is_active', true);

                        if ($companyId) {
                            $query->where(function ($q) use ($companyId) {
                                $q->where('company_id', $companyId)
                                    ->orWhereNull('company_id');
                            });
                        } else {
                            $query->whereNull('company_id');
                        }

                        $providerLabels = [
                            'laravel_mail' => 'Laravel Mail',
                            'mailtrap' => 'Mailtrap',
                            'twilio' => 'Twilio',
                            'mnotify' => 'mNotify',
                            'wasender' => 'Wasender',
                            'filament_database' => 'Filament Database',
                        ];

                        return $query->get()
                            ->mapWithKeys(function ($config) use ($providerLabels) {
                                $label = $providerLabels[$config->provider] ?? ucfirst(str_replace('_', ' ', $config->provider));

                                return [$config->provider => "{$label} ({$config->name})"];
                            })
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('language')
                    ->options([
                        'en' => 'English',
                        'fr' => 'French',
                        'es' => 'Spanish',
                        'de' => 'German',
                        'pt' => 'Portuguese',
                        'it' => 'Italian',
                        'ru' => 'Russian',
                        'zh' => 'Chinese',
                        'ja' => 'Japanese',
                        'ar' => 'Arabic',
                        'hi' => 'Hindi',
                        'sw' => 'Swahili',
                    ])
                    ->default('en')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->maxLength(255),
                Forms\Components\Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('language')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
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
            'index' => Pages\ListCommTemplates::route('/'),
            'create' => Pages\CreateCommTemplate::route('/create'),
            'edit' => Pages\EditCommTemplate::route('/{record}/edit'),
        ];
    }
}

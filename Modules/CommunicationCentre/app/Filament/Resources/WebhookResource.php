<?php

namespace Modules\CommunicationCentre\Filament\Resources;

use Filament\Actions\Action;
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
use Modules\CommunicationCentre\Filament\Resources\WebhookResource\Pages;
use Modules\CommunicationCentre\Models\Webhook;

class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|\UnitEnum|null $navigationGroup = 'Communication Settings';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->components([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('secret')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => \Illuminate\Support\Str::random(40))
                            ->columnSpanFull(),
                    ]),

                Section::make('Configuration')
                    ->components([
                        Forms\Components\Select::make('provider')
                            ->options([
                                'mailgun' => 'Mailgun',
                                'twilio' => 'Twilio',
                                'whatsapp' => 'WhatsApp Business',
                                'custom' => 'Custom',
                            ])
                            ->default('custom')
                            ->live(),

                        Forms\Components\CheckboxList::make('events')
                            ->options([
                                'message.sent' => 'Message Sent',
                                'message.delivered' => 'Message Delivered',
                                'message.failed' => 'Message Failed',
                                'message.read' => 'Message Read',
                                'template.created' => 'Template Created',
                                'template.updated' => 'Template Updated',
                                'template.deleted' => 'Template Deleted',
                            ])
                            ->columns(2)
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('headers')
                            ->keyLabel('Header Name')
                            ->valueLabel('Header Value')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('timeout')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(120)
                            ->default(30),

                        Forms\Components\TextInput::make('retry_attempts')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->default(3),
                    ])->columns(2),

                Section::make('Status')
                    ->components([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->url),

                Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mailgun'  => 'success',
                        'twilio'   => 'warning',
                        'whatsapp' => 'info',
                        default    => 'primary',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_called_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_response_status')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'warning',
                        $state >= 400                 => 'danger',
                        default                       => 'gray',
                    })
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\SelectFilter::make('provider')
                    ->options([
                        'mailgun' => 'Mailgun',
                        'twilio' => 'Twilio',
                        'whatsapp' => 'WhatsApp Business',
                        'custom' => 'Custom',
                    ]),
            ])
            ->actions([
                Action::make('test')
                    ->icon('heroicon-o-play')
                    ->action(fn (Webhook $record) => $record->testWebhook()),

                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListWebhooks::route('/'),
            'create' => Pages\CreateWebhook::route('/create'),
            'edit' => Pages\EditWebhook::route('/{record}/edit'),
        ];
    }
}

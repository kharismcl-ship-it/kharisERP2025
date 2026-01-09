<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\CommunicationCentre\Models\CommTemplate;
use Modules\Hostels\Filament\Resources\HostelTemplateResource\Pages;

class HostelTemplateResource extends Resource
{
    protected static ?string $model = CommTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?string $navigationLabel = 'Hostel Templates';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->live()
                    ->required(),

                Forms\Components\Select::make('code')
                    ->options(function () {
                        // Dynamically fetch template codes from notification classes
                        return self::getTemplateOptions();
                    })
                    ->required()
//                    ->unique(ignoreRecord: true)
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        // Automatically populate placeholders when template code changes
                        if ($state) {
                            $placeholders = self::getPlaceholdersForTemplate($state);
                            $set('placeholders', implode("\n", $placeholders));
                        }
                    }),

                Forms\Components\Select::make('channel')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                    ])
                    ->required(),
                Forms\Components\Select::make('provider')
                    ->relationship('providerConfig', 'name', function ($query, callable $get) {
                        $companyId = $get('company_id');
                        if ($companyId) {
                            $query->where('company_id', $companyId);
                        }
                    })
                    ->required()
                    ->live()
                    ->preload(),

                // Replace the current content field with:
                Forms\Components\TextInput::make('subject')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('body')
                    ->required()
                    ->autosize()
                    ->extraAttributes(['id' => 'template-content'])
                    ->hint(function (Get $get) {
                        $code = $get('code');
                        if ($code) {
                            $placeholders = self::getPlaceholdersForTemplate($code);
                            if (! empty($placeholders)) {
                                return 'Available placeholders: '.implode(', ', array_map(fn ($p) => "{{{$p}}}", $placeholders));
                            }
                        }

                        return null;
                    }),
                Forms\Components\Textarea::make('placeholders')
                    ->label('Placeholders')
                    ->disabled()
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->default(true),

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('providerConfig.name')
                    ->label('Provider')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                    ]),
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
            'index' => Pages\ListHostelTemplates::route('/'),
            'create' => Pages\CreateHostelTemplate::route('/create'),
            'edit' => Pages\EditHostelTemplate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $templateCodes = array_keys(self::getTemplateOptions());

        return parent::getEloquentQuery()
            ->whereIn('code', $templateCodes);
    }

    protected static function getPlaceholdersForTemplate(string $templateCode): array
    {
        $notifications = self::getNotificationClasses();

        foreach ($notifications as $notificationClass) {
            if (method_exists($notificationClass, 'getTemplateCode') &&
                $notificationClass::getTemplateCode() === $templateCode &&
                method_exists($notificationClass, 'getPlaceholders')) {
                return $notificationClass::getPlaceholders();
            }
        }

        return [];
    }

    protected static function getTemplateOptions(): array
    {
        $notifications = self::getNotificationClasses();
        $options = [];

        foreach ($notifications as $notificationClass) {
            if (method_exists($notificationClass, 'getTemplateCode') &&
                method_exists($notificationClass, 'getTemplateName')) {
                $code = $notificationClass::getTemplateCode();
                $name = $notificationClass::getTemplateName();
                $options[$code] = $name;
            }
        }

        return $options;
    }

    protected static function getNotificationClasses(): array
    {
        return [
            \Modules\Hostels\Notifications\BookingConfirmationNotification::class,
            \Modules\Hostels\Notifications\CheckInNotification::class,
            \Modules\Hostels\Notifications\PaymentReceiptNotification::class,
            \Modules\Hostels\Notifications\CheckoutReminderNotification::class,
            \Modules\Hostels\Notifications\MaintenanceRequestNotification::class,
        ];
    }
}

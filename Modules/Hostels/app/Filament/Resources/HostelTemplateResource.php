<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\CommunicationCentre\Models\CommTemplate;
use Modules\Hostels\Filament\Resources\HostelTemplateResource\Pages;

class HostelTemplateResource extends Resource
{
    protected static ?string $model = CommTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Hostel Settings';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if (!in_array($value, [
                                    'booking_confirmation',
                                    'check_in_notification',
                                    'payment_receipt',
                                    'checkout_reminder'
                                ])) {
                                    $fail('The template code must be one of the predefined hostel template codes.');
                                }
                            };
                        }
                    ]),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('channel')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS'
                    ])
                    ->required(),
                Forms\Components\Select::make('provider')
                    ->relationship('providerConfig', 'name')
                    ->required(),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('placeholders')
                    ->label('Available Placeholders')
                    ->columnSpanFull()
                    ->helperText('List of available placeholders for this template')
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
                        'sms' => 'SMS'
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
        return parent::getEloquentQuery()
            ->whereIn('code', [
                'booking_confirmation',
                'check_in_notification',
                'payment_receipt',
                'checkout_reminder'
            ]);
    }
}

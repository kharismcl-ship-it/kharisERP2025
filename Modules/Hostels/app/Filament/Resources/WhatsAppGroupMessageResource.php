<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource\Pages;
use Modules\Hostels\Models\WhatsAppGroupMessage;

class WhatsAppGroupMessageResource extends Resource
{
    protected static ?string $model = WhatsAppGroupMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('whatsapp_group_id')
                    ->relationship('whatsappGroup', 'name')
                    ->required(),
                Forms\Components\Select::make('sender_hostel_occupant_id')
                    ->relationship('sender', 'name')
                    ->required(),
                Forms\Components\TextInput::make('message_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('media_url')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('sent_at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('whatsappGroup.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sender.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('message_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
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
            'index' => Pages\ListWhatsAppGroupMessages::route('/'),
            'create' => Pages\CreateWhatsAppGroupMessage::route('/create'),
            'edit' => Pages\EditWhatsAppGroupMessage::route('/{record}/edit'),
        ];
    }
}

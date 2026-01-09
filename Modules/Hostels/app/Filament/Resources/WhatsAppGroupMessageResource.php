<?php

namespace Modules\Hostels\Filament\Resources;

use Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource\Pages;
use Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource\RelationManagers;
use Modules\Hostels\Models\WhatsAppGroupMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WhatsAppGroupMessageResource extends Resource
{
    protected static ?string $model = WhatsAppGroupMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Hostel Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('whatsapp_group_id')
                    ->relationship('whatsappGroup', 'name')
                    ->required(),
                Forms\Components\Select::make('sender_tenant_id')
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
            'index' => Pages\ListWhatsAppGroupMessages::route('/'),
            'create' => Pages\CreateWhatsAppGroupMessage::route('/create'),
            'edit' => Pages\EditWhatsAppGroupMessage::route('/{record}/edit'),
        ];
    }
}

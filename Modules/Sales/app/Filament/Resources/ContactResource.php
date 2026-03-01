<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\ContactResource\Pages;
use Modules\Sales\Models\SalesContact;
use Modules\Sales\Models\SalesOrganization;

class ContactResource extends Resource
{
    protected static ?string $model = SalesContact::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-users';
    protected static string|\UnitEnum|null   $navigationGroup = 'CRM';
    protected static ?int                    $navigationSort  = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contact Details')->columns(2)->schema([
                TextInput::make('first_name')->required()->maxLength(100),
                TextInput::make('last_name')->maxLength(100),
                TextInput::make('email')->email()->maxLength(255),
                TextInput::make('phone')->tel()->maxLength(50),
                TextInput::make('whatsapp_number')->label('WhatsApp')->tel()->maxLength(50),
                TextInput::make('job_title')->maxLength(100),
                Select::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                    ])
                    ->columnSpanFull(),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->label('First Name')->searchable()->sortable(),
                TextColumn::make('last_name')->label('Last Name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('organization.name')->label('Organization'),
                TextColumn::make('job_title'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name'),
            ])
            ->recordActions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit'   => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
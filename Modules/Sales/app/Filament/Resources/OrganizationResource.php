<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\OrganizationResource\Pages;
use Modules\Sales\Models\SalesOrganization;

class OrganizationResource extends Resource
{
    protected static ?string $model = SalesOrganization::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-building-office-2';
    protected static string|\UnitEnum|null   $navigationGroup = 'CRM';
    protected static ?int                    $navigationSort  = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Organization Details')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('industry')->maxLength(100),
                TextInput::make('website')->url()->maxLength(255),
                TextInput::make('email')->email()->maxLength(255),
                TextInput::make('phone')->tel()->maxLength(50),
                TextInput::make('city')->maxLength(100),
                TextInput::make('country')->default('Ghana')->maxLength(100),
                TextInput::make('currency')->default('GHS')->maxLength(10),
                TextInput::make('credit_limit')->numeric()->prefix('GHS')->default(0),
                TextInput::make('payment_terms')->numeric()->suffix('days')->default(30),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('industry'),
                TextColumn::make('city'),
                TextColumn::make('country'),
                TextColumn::make('payment_terms')->suffix(' days'),
                TextColumn::make('credit_limit')->money('GHS'),
                TextColumn::make('contacts_count')->counts('contacts')->label('Contacts'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit'   => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
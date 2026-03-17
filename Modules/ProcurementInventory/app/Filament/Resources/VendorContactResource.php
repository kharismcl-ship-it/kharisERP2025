<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Models\Vendor;
use Modules\ProcurementInventory\Models\VendorContact;

class VendorContactResource extends Resource
{
    protected static ?string $model = VendorContact::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Vendor Contacts';

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Vendor Portal Contact')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('vendor_id')
                        ->label('Vendor')
                        ->relationship('vendor', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('name')->required()->maxLength(100),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(VendorContact::class, 'email', ignorable: fn ($record) => $record),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn ($record) => $record === null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->label('Password (leave blank to keep current)'),
                    Forms\Components\TextInput::make('phone')->nullable(),
                    Forms\Components\TextInput::make('job_title')->nullable(),
                    Forms\Components\Toggle::make('is_primary')->label('Primary Contact')->inline(false),
                    Forms\Components\Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone')->placeholder('—'),
                Tables\Columns\TextColumn::make('job_title')->placeholder('—'),
                Tables\Columns\IconColumn::make('is_primary')->label('Primary')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\ProcurementInventory\Filament\Resources\VendorContactResource\Pages\ListVendorContacts::route('/'),
            'create' => \Modules\ProcurementInventory\Filament\Resources\VendorContactResource\Pages\CreateVendorContact::route('/create'),
            'edit'   => \Modules\ProcurementInventory\Filament\Resources\VendorContactResource\Pages\EditVendorContact::route('/{record}/edit'),
        ];
    }
}

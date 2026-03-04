<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Modules\ProcurementInventory\Filament\Resources\VendorResource\Pages;
use Modules\ProcurementInventory\Models\Vendor;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Basic Information')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        
                        ->searchable(),

                    Forms\Components\TextInput::make('name')
                        
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        
                        ->maxLength(255),

                    Forms\Components\Select::make('status')
                        ->options(['active' => 'Active', 'inactive' => 'Inactive', 'blocked' => 'Blocked'])
                        ->default('active')
                        ->required(),

                    Forms\Components\TextInput::make('email')->email(),
                    Forms\Components\TextInput::make('phone'),
                    Forms\Components\TextInput::make('tax_number')->label('Tax / VAT Number'),
                    Forms\Components\TextInput::make('currency')->default('GHS')->maxLength(10),
                ]),

            Forms\Components\Section::make('Address')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('address')->rows(2),
                    Forms\Components\TextInput::make('city'),
                    Forms\Components\TextInput::make('country')->default('Ghana'),
                ]),

            Forms\Components\Section::make('Contact Person')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('contact_person'),
                    Forms\Components\TextInput::make('contact_phone'),
                    Forms\Components\TextInput::make('contact_email')->email(),
                ]),

            Forms\Components\Section::make('Payment & Banking')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('payment_terms')
                        ->numeric()
                        ->default(30)
                        ->suffix('days'),
                    Forms\Components\TextInput::make('bank_name'),
                    Forms\Components\TextInput::make('bank_account_number'),
                    Forms\Components\TextInput::make('bank_branch'),
                ]),

            Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('contact_person')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_terms')
                    ->suffix(' days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'warning',
                        'blocked'  => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('purchase_orders_count')
                    ->counts('purchaseOrders')
                    ->label('POs'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'blocked' => 'Blocked']),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \Modules\ProcurementInventory\Filament\Resources\VendorResource\RelationManagers\PurchaseOrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view'   => Pages\ViewVendor::route('/{record}'),
            'edit'   => Pages\EditVendor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'contact_person'];
    }
}

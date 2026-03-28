<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\CustomerResource\Pages;
use Modules\Finance\Models\Customer;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company & Code')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('customer_code')
                            ->label('Customer Code')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('customer_type')
                            ->options([
                                'individual'  => 'Individual',
                                'company'     => 'Company',
                                'government'  => 'Government',
                            ])
                            ->required()
                            ->default('individual'),
                    ]),

                Section::make('Contact Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                Section::make('Credit & Terms')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('credit_limit')
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0),
                        Forms\Components\Select::make('payment_terms')
                            ->options([
                                'immediate' => 'Immediate',
                                'net7'      => 'Net 7',
                                'net14'     => 'Net 14',
                                'net30'     => 'Net 30',
                                'net60'     => 'Net 60',
                            ])
                            ->default('immediate'),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')->rows(3),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_code')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('customer_type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'individual' => 'info',
                        'company'    => 'warning',
                        'government' => 'success',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('credit_limit')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_terms')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_type')
                    ->options([
                        'individual' => 'Individual',
                        'company'    => 'Company',
                        'government' => 'Government',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\InvoiceReminderRuleResource\Pages;
use Modules\Finance\Models\InvoiceReminderRule;

class InvoiceReminderRuleResource extends Resource
{
    protected static ?string $model = InvoiceReminderRule::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Reminder Rules';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('days_offset')
                            ->numeric()
                            ->required()
                            ->label('Days Offset')
                            ->helperText('Negative = before due date (e.g. -3 = 3 days before). Positive = overdue (e.g. 7 = 7 days late).'),
                        Forms\Components\Textarea::make('template')
                            ->required()
                            ->columnSpanFull()
                            ->rows(5)
                            ->helperText('Available merge tags: {customer_name}, {invoice_number}, {amount}, {due_date}, {days_overdue}'),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('days_offset')
                    ->label('Timing')
                    ->formatStateUsing(fn (int $state) => $state < 0
                        ? abs($state) . ' days before due'
                        : $state . ' days after due'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ActionGroup::make([
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
            'index'  => Pages\ListInvoiceReminderRules::route('/'),
            'create' => Pages\CreateInvoiceReminderRule::route('/create'),
            'edit'   => Pages\EditInvoiceReminderRule::route('/{record}/edit'),
        ];
    }
}
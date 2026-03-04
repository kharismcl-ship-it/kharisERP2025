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
use Modules\Finance\Filament\Resources\InvoiceResource\Pages;
use Modules\Finance\Filament\Resources\InvoiceResource\RelationManagers\InvoiceLinesRelationManager;
use Modules\Finance\Filament\Resources\InvoiceResource\RelationManagers\PaymentsRelationManager;
use Modules\Finance\Models\Invoice;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Info')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('customer_name')
                            
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_type')
                            ->maxLength(255)
                            ->placeholder('e.g. student, resident, corporate'),
                        Forms\Components\TextInput::make('customer_id')
                            ->label('Customer ID')
                            ->numeric()
                            ->placeholder('Internal customer record ID'),
                    ]),

                Section::make('Invoice Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'sent'      => 'Sent',
                                'paid'      => 'Paid',
                                'overdue'   => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\DatePicker::make('invoice_date')
                            ->required(),
                        Forms\Components\DatePicker::make('due_date'),
                    ]),

                Section::make('Amounts')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('sub_total')
                            ->required()
                            ->numeric()
                            ->prefix('GHS'),
                        Forms\Components\TextInput::make('tax_total')
                            ->required()
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0),
                        Forms\Components\TextInput::make('total')
                            ->required()
                            ->numeric()
                            ->prefix('GHS'),
                    ]),

                Section::make('Module Reference')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('hostel_id')
                            ->label('Hostel ID')
                            ->numeric(),
                        Forms\Components\TextInput::make('farm_id')
                            ->label('Farm ID')
                            ->numeric(),
                        Forms\Components\TextInput::make('construction_project_id')
                            ->label('Construction Project ID')
                            ->numeric(),
                        Forms\Components\TextInput::make('plant_id')
                            ->label('Plant / Manufacturing ID')
                            ->numeric(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'     => 'gray',
                        'sent'      => 'info',
                        'paid'      => 'success',
                        'overdue'   => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'sent'      => 'Sent',
                        'paid'      => 'Paid',
                        'overdue'   => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
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
        return [
            InvoiceLinesRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view'   => Pages\ViewInvoice::route('/{record}'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}

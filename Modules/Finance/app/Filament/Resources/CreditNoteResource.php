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
use Modules\Finance\Filament\Resources\CreditNoteResource\Pages;
use Modules\Finance\Models\CreditNote;

class CreditNoteResource extends Resource
{
    protected static ?string $model = CreditNote::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMinus;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationLabel = 'Credit Notes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Info')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('customer_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_type')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_id_ref')
                            ->label('Customer Ref')
                            ->maxLength(255),
                        Forms\Components\Select::make('invoice_id')
                            ->relationship('invoice', 'invoice_number')
                            ->searchable()
                            ->placeholder('Link to invoice (optional)'),
                    ]),

                Section::make('Credit Note Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('credit_note_number')
                            ->label('Credit Note Number')
                            ->disabled()
                            ->placeholder('Auto-generated'),
                        Forms\Components\DatePicker::make('issue_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'issued'    => 'Issued',
                                'applied'   => 'Applied',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\Textarea::make('reason')->columnSpanFull()->rows(2),
                        Forms\Components\Textarea::make('notes')->columnSpanFull()->rows(2),
                    ]),

                Section::make('Lines')
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->relationship('lines')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('description')->required()->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')->numeric()->default(1),
                                Forms\Components\TextInput::make('unit_price')->numeric()->prefix('GHS'),
                            ]),
                    ]),

                Section::make('Totals')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('sub_total')->numeric()->prefix('GHS')->default(0),
                        Forms\Components\TextInput::make('tax_total')->numeric()->prefix('GHS')->default(0),
                        Forms\Components\TextInput::make('total')->numeric()->prefix('GHS')->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('credit_note_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'     => 'gray',
                        'issued'    => 'info',
                        'applied'   => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'issued'    => 'Issued',
                        'applied'   => 'Applied',
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCreditNotes::route('/'),
            'create' => Pages\CreateCreditNote::route('/create'),
            'view'   => Pages\ViewCreditNote::route('/{record}'),
            'edit'   => Pages\EditCreditNote::route('/{record}/edit'),
        ];
    }
}
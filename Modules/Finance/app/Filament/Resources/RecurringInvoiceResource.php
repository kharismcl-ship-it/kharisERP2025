<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\RecurringInvoiceResource\Pages;
use Modules\Finance\Models\RecurringInvoice;
use Modules\Finance\Services\RecurringInvoiceService;

class RecurringInvoiceResource extends Resource
{
    protected static ?string $model = RecurringInvoice::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPathRoundedSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 35;

    protected static ?string $navigationLabel = 'Recurring Invoices';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Customer')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->maxLength(255),
                    ]),

                Section::make('Invoice Template')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('Description that appears on each generated invoice'),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('GHS'),
                        Forms\Components\TextInput::make('tax_total')
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0),
                    ]),

                Section::make('Schedule')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('frequency')
                            ->options(RecurringInvoice::FREQUENCIES)
                            ->required(),
                        Forms\Components\TextInput::make('day_of_month')
                            ->label('Day of Month (for monthly)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(28)
                            ->placeholder('e.g. 1 for 1st of month'),
                        Forms\Components\DatePicker::make('start_date')->required(),
                        Forms\Components\DatePicker::make('next_run_date')
                            ->required()
                            ->label('First Invoice Date'),
                        Forms\Components\DatePicker::make('end_date')
                            ->placeholder('Leave blank for indefinite'),
                        Forms\Components\Select::make('status')
                            ->options(RecurringInvoice::STATUSES)
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('next_run_date')
                    ->date()
                    ->sortable()
                    ->label('Next Invoice'),
                Tables\Columns\TextColumn::make('invoices_generated')
                    ->label('Generated')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'    => 'success',
                        'paused'    => 'warning',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(RecurringInvoice::STATUSES),
                Tables\Filters\SelectFilter::make('frequency')
                    ->options(RecurringInvoice::FREQUENCIES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('generate_now')
                        ->label('Generate Now')
                        ->icon(Heroicon::OutlinedBolt)
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (RecurringInvoice $record) => $record->isActive())
                        ->action(function (RecurringInvoice $record, RecurringInvoiceService $service) {
                            $invoice = $service->generate($record);
                            Notification::make()
                                ->title("Invoice #{$invoice->invoice_number} generated.")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRecurringInvoices::route('/'),
            'create' => Pages\CreateRecurringInvoice::route('/create'),
            'view'   => Pages\ViewRecurringInvoice::route('/{record}'),
            'edit'   => Pages\EditRecurringInvoice::route('/{record}/edit'),
        ];
    }
}

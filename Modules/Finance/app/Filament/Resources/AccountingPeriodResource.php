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
use Modules\Finance\Filament\Resources\AccountingPeriodResource\Pages;
use Modules\Finance\Models\AccountingPeriod;

class AccountingPeriodResource extends Resource
{
    protected static ?string $model = AccountingPeriod::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 46;

    protected static ?string $navigationLabel = 'Accounting Periods';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Period Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->preload()
                            
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            
                            ->maxLength(100)
                            ->placeholder('e.g. January 2026')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\DatePicker::make('end_date'),
                        Forms\Components\Select::make('status')
                            ->options(AccountingPeriod::STATUSES)
                            ->default('open')
                            ->required(),
                    ]),

                Section::make('Notes')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Optional closing notes or comments'),
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
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'open'    => 'success',
                        'closing' => 'warning',
                        'closed'  => 'gray',
                        default   => 'gray',
                    }),
                Tables\Columns\TextColumn::make('closedBy.name')
                    ->label('Closed By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('closed_at')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AccountingPeriod::STATUSES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('close_period')
                        ->label('Close Period')
                        ->icon(Heroicon::OutlinedLockClosed)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Close Accounting Period')
                        ->modalDescription('This will lock all journal entries in this period. This action cannot be undone.')
                        ->visible(fn (AccountingPeriod $record) => $record->isOpen())
                        ->action(function (AccountingPeriod $record) {
                            $record->close(auth()->id());
                            Notification::make()
                                ->title('Period closed and all journals locked.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAccountingPeriods::route('/'),
            'create' => Pages\CreateAccountingPeriod::route('/create'),
            'view'   => Pages\ViewAccountingPeriod::route('/{record}'),
            'edit'   => Pages\EditAccountingPeriod::route('/{record}/edit'),
        ];
    }
}

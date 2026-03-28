<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\JournalEntryLogResource\Pages;
use Modules\Finance\Models\JournalEntryLog;

class JournalEntryLogResource extends Resource
{
    protected static ?string $model = JournalEntryLog::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 95;

    protected static ?string $navigationLabel = 'Audit Trail';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date / Time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('journal_entry_id')
                    ->label('Entry #')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'created'      => 'success',
                        'edited'       => 'warning',
                        'approved'     => 'info',
                        'reversed'     => 'danger',
                        'period_closed' => 'gray',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('field_changed')
                    ->label('Field')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('old_value')
                    ->label('Old Value')
                    ->limit(40)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('new_value')
                    ->label('New Value')
                    ->limit(40)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'created'       => 'Created',
                        'edited'        => 'Edited',
                        'approved'      => 'Approved',
                        'reversed'      => 'Reversed',
                        'period_closed' => 'Period Closed',
                    ]),
            ])
            ->actions([
                // Read-only — no actions
            ])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntryLogs::route('/'),
        ];
    }
}
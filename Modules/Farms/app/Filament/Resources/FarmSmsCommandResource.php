<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Modules\Farms\Models\FarmSmsCommand;

class FarmSmsCommandResource extends Resource
{
    protected static ?string $model = FarmSmsCommand::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';
    protected static string|\UnitEnum|null $navigationGroup = 'Precision Agriculture';
    protected static ?string $navigationLabel = 'SMS / USSD Log';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone_number')->searchable()->sortable(),
                TextColumn::make('farmWorker.employee.full_name')
                    ->label('Worker')
                    ->placeholder('—'),
                TextColumn::make('command_type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'TASK'    => 'info',
                        'ATTEND'  => 'success',
                        'REPORT'  => 'warning',
                        'HELP'    => 'gray',
                        default   => 'gray',
                    })
                    ->placeholder('—'),
                TextColumn::make('raw_message')
                    ->limit(60)
                    ->tooltip(fn ($record): string => $record->raw_message ?? ''),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'received'  => 'gray',
                        'processed' => 'success',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Farms\Filament\Resources\FarmSmsCommandResource\Pages\ListFarmSmsCommands::route('/'),
        ];
    }
}
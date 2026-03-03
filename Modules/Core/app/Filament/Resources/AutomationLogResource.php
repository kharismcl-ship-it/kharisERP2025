<?php

namespace Modules\Core\Filament\Resources;

use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\AutomationLogResource\Pages;
use Modules\Core\Models\AutomationLog;
use UnitEnum;

class AutomationLogResource extends Resource
{
    protected static ?string $model = AutomationLog::class;

    protected static ?string $slug = 'automation-logs';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 5;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Run Details')->schema([
                TextEntry::make('automationSetting.module')->badge()->label('Module'),
                TextEntry::make('automationSetting.action')->label('Action'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'completed' => 'success',
                        'failed'    => 'danger',
                        'running'   => 'warning',
                        default     => 'gray',
                    }),
                TextEntry::make('started_at')->dateTime(),
                TextEntry::make('completed_at')->dateTime(),
                TextEntry::make('execution_time')->suffix('s')->label('Execution Time (s)'),
                TextEntry::make('records_processed')->label('Records Processed'),
                TextEntry::make('error_message')->columnSpanFull(),
            ])->columns(2),
            Section::make('Details')->schema([
                TextEntry::make('details')
                    ->state(fn ($record) => $record->details ? json_encode($record->details, JSON_PRETTY_PRINT) : '—')
                    ->prose()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('automationSetting.module')
                    ->badge()
                    ->label('Module')
                    ->sortable(),
                Tables\Columns\TextColumn::make('automationSetting.action')
                    ->label('Action')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'completed' => 'success',
                        'failed'    => 'danger',
                        'running'   => 'warning',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('execution_time')
                    ->suffix('s')
                    ->label('Exec. Time'),
                Tables\Columns\TextColumn::make('records_processed')
                    ->label('Records'),
                Tables\Columns\TextColumn::make('error_message')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record?->error_message),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'failed'    => 'Failed',
                        'running'   => 'Running',
                    ]),
                Tables\Filters\Filter::make('started_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('From'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('started_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('started_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->defaultSort('started_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutomationLogs::route('/'),
            'view'  => Pages\ViewAutomationLog::route('/{record}'),
        ];
    }
}

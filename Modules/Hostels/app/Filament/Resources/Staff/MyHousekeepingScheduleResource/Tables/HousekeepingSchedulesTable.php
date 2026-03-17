<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyHousekeepingScheduleResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Hostels\Models\HostelHousekeepingSchedule;

class HousekeepingSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('schedule_date')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->color(fn (HostelHousekeepingSchedule $r) => $r->isOverdue() ? 'danger' : null),

                Tables\Columns\TextColumn::make('hostel.name')
                    ->label('Hostel'),

                Tables\Columns\TextColumn::make('room.room_number')
                    ->label('Room')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('cleaning_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state ?? '—'))),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'     => 'warning',
                        'in_progress' => 'info',
                        'completed'   => 'success',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
            ])
            ->defaultSort('schedule_date', 'asc')
            ->actions([
                ViewAction::make(),
                Action::make('start')
                    ->label('Start')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn (HostelHousekeepingSchedule $record) => $record->markAsInProgress())
                    ->visible(fn (HostelHousekeepingSchedule $record) => $record->status === 'pending'),
                Action::make('complete')
                    ->label('Mark Done')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Completed?')
                    ->action(fn (HostelHousekeepingSchedule $record) => $record->markAsCompleted())
                    ->visible(fn (HostelHousekeepingSchedule $record) => $record->status === 'in_progress'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'     => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                    ]),
            ]);
    }
}

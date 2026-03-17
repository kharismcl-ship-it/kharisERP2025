<?php

namespace Modules\HR\Filament\Resources\Staff\MyShiftScheduleResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;

class ShiftScheduleTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shift.name')
                    ->label('Shift Name')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('shift.start_time')
                    ->label('Start Time')
                    ->time('g:i A'),
                Tables\Columns\TextColumn::make('shift.end_time')
                    ->label('End Time')
                    ->time('g:i A'),
                Tables\Columns\TextColumn::make('shift.days_of_week')
                    ->label('Days')
                    ->getStateUsing(fn ($record) => $record->shift?->day_names ?? '—'),
                Tables\Columns\TextColumn::make('effective_from')
                    ->label('Effective From')
                    ->date()->sortable(),
                Tables\Columns\TextColumn::make('effective_to')
                    ->label('Effective To')
                    ->date()->placeholder('Ongoing'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}

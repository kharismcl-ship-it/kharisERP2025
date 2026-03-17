<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmAttendanceResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class FarmAttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('farm.name')
                    ->label('Farm')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'present'  => 'success',
                        'absent'   => 'danger',
                        'half_day' => 'warning',
                        'leave'    => 'info',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours')
                    ->suffix(' hrs')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('overtime_hours')
                    ->label('OT')
                    ->suffix(' hrs')
                    ->placeholder('—'),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->actions([
                ViewAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present'  => 'Present',
                        'absent'   => 'Absent',
                        'half_day' => 'Half Day',
                        'leave'    => 'Leave',
                    ]),
            ]);
    }
}

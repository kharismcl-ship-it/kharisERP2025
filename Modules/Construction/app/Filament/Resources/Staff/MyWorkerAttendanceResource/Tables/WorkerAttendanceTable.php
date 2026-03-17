<?php

namespace Modules\Construction\Filament\Resources\Staff\MyWorkerAttendanceResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;

class WorkerAttendanceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('attendance_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present'  => 'success',
                        'absent'   => 'danger',
                        'half_day' => 'warning',
                        'excused'  => 'info',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Check In')
                    ->time('H:i')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('check_out_time')
                    ->label('Check Out')
                    ->time('H:i')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('per_diem_amount')
                    ->label('Per Diem')
                    ->money('KES')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),
            ])
            ->actions([ViewAction::make()])
            ->filters([
                Tables\Filters\SelectFilter::make('attendance_status')
                    ->options([
                        'present'  => 'Present',
                        'absent'   => 'Absent',
                        'half_day' => 'Half Day',
                        'excused'  => 'Excused',
                    ]),
            ]);
    }
}

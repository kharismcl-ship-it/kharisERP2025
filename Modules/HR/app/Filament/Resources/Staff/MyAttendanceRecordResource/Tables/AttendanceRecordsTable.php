<?php

namespace Modules\HR\Filament\Resources\Staff\MyAttendanceRecordResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\HR\Models\AttendanceRecord;

class AttendanceRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('D, M d, Y')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present'   => 'success',
                        'late'      => 'warning',
                        'half_day'  => 'info',
                        'absent'    => 'danger',
                        'on_leave'  => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Clock In')
                    ->time('g:i A')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('check_out_time')
                    ->label('Clock Out')
                    ->time('g:i A')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Hours')
                    ->getStateUsing(function (AttendanceRecord $r): string {
                        if ($r->check_in_time && $r->check_out_time) {
                            $mins = $r->check_in_time->diffInMinutes($r->check_out_time);
                            return sprintf('%dh %dm', intdiv($mins, 60), $mins % 60);
                        }
                        return '—';
                    }),

                Tables\Columns\TextColumn::make('notes')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([ViewAction::make()])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present'  => 'Present',
                        'late'     => 'Late',
                        'half_day' => 'Half Day',
                        'absent'   => 'Absent',
                        'on_leave' => 'On Leave',
                    ]),
            ]);
    }
}

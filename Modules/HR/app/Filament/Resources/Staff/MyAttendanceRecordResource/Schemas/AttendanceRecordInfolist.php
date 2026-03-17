<?php

namespace Modules\HR\Filament\Resources\Staff\MyAttendanceRecordResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\AttendanceRecord;

class AttendanceRecordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attendance Record')
                ->columns(3)
                ->schema([
                    TextEntry::make('date')
                        ->date('l, F j, Y')
                        ->weight('bold'),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'present'  => 'success',
                            'late'     => 'warning',
                            'half_day' => 'info',
                            'absent'   => 'danger',
                            'on_leave' => 'gray',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn (string $state) => ucwords(str_replace('_', ' ', $state))),

                    TextEntry::make('duration')
                        ->label('Hours Worked')
                        ->getStateUsing(function (AttendanceRecord $r): string {
                            if ($r->check_in_time && $r->check_out_time) {
                                $mins = $r->check_in_time->diffInMinutes($r->check_out_time);
                                return sprintf('%dh %dm', intdiv($mins, 60), $mins % 60);
                            }
                            return '—';
                        }),

                    TextEntry::make('check_in_time')
                        ->label('Clock In')
                        ->dateTime('g:i A')
                        ->placeholder('—'),

                    TextEntry::make('check_out_time')
                        ->label('Clock Out')
                        ->dateTime('g:i A')
                        ->placeholder('—'),

                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}

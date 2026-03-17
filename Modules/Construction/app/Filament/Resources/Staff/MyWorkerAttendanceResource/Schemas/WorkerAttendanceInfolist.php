<?php

namespace Modules\Construction\Filament\Resources\Staff\MyWorkerAttendanceResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkerAttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attendance Record')
                ->columns(3)
                ->schema([
                    TextEntry::make('date')->date()->weight('bold'),
                    TextEntry::make('attendance_status')
                        ->label('Status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'present'  => 'success',
                            'absent'   => 'danger',
                            'half_day' => 'warning',
                            'excused'  => 'info',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
                    TextEntry::make('project.name')->label('Project')->placeholder('—'),
                    TextEntry::make('check_in_time')->label('Check In')->time('H:i')->placeholder('—'),
                    TextEntry::make('check_out_time')->label('Check Out')->time('H:i')->placeholder('—'),
                    TextEntry::make('hours_worked')->label('Hours Worked')->placeholder('—'),
                    TextEntry::make('per_diem_amount')->label('Per Diem')->money('KES')->placeholder('—'),
                    IconEntry::make('is_approved')->label('Approved')->boolean(),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}

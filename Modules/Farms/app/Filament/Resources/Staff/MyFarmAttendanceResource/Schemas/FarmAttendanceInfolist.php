<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmAttendanceResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FarmAttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attendance Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('attendance_date')
                        ->label('Date')
                        ->date(),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'present'  => 'success',
                            'absent'   => 'danger',
                            'half_day' => 'warning',
                            'leave'    => 'info',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                    TextEntry::make('farm.name')
                        ->label('Farm')
                        ->placeholder('—'),

                    TextEntry::make('hours_worked')
                        ->label('Hours Worked')
                        ->suffix(' hrs')
                        ->placeholder('—'),

                    TextEntry::make('overtime_hours')
                        ->label('Overtime')
                        ->suffix(' hrs')
                        ->placeholder('—'),

                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}

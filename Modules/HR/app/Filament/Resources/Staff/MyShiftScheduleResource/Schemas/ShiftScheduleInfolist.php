<?php

namespace Modules\HR\Filament\Resources\Staff\MyShiftScheduleResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShiftScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Shift Assignment')
                ->columns(2)
                ->schema([
                    TextEntry::make('shift.name')
                        ->label('Shift Name'),
                    TextEntry::make('shift.start_time')
                        ->label('Start Time')
                        ->placeholder('—'),
                    TextEntry::make('shift.end_time')
                        ->label('End Time')
                        ->placeholder('—'),
                    TextEntry::make('effective_from')
                        ->date(),
                    TextEntry::make('effective_to')
                        ->date()
                        ->placeholder('Ongoing'),
                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}

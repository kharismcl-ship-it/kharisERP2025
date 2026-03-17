<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyHousekeepingScheduleResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HousekeepingScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Schedule Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('schedule_date')
                        ->label('Date')
                        ->date(),

                    TextEntry::make('cleaning_type')
                        ->label('Cleaning Type')
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state ?? '—'))),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'pending'     => 'warning',
                            'in_progress' => 'info',
                            'completed'   => 'success',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                    TextEntry::make('hostel.name')
                        ->label('Hostel'),

                    TextEntry::make('room.room_number')
                        ->label('Room')
                        ->placeholder('—'),

                    TextEntry::make('quality_score')
                        ->label('Quality Score')
                        ->suffix('/10')
                        ->placeholder('—'),

                    TextEntry::make('started_at')
                        ->label('Started At')
                        ->dateTime()
                        ->placeholder('—'),

                    TextEntry::make('completed_at')
                        ->label('Completed At')
                        ->dateTime()
                        ->placeholder('—'),

                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}

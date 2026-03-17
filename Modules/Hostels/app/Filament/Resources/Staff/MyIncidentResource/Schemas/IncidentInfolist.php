<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IncidentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Incident Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('title')
                        ->columnSpanFull()
                        ->weight('bold'),

                    TextEntry::make('severity')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'critical' => 'danger',
                            'major'    => 'warning',
                            'minor'    => 'info',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'open'      => 'warning',
                            'escalated' => 'danger',
                            'resolved'  => 'success',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('reported_at')
                        ->label('Reported At')
                        ->dateTime()
                        ->placeholder('—'),

                    TextEntry::make('hostel.name')
                        ->label('Hostel'),

                    TextEntry::make('room.room_number')
                        ->label('Room')
                        ->placeholder('—'),

                    TextEntry::make('occupant.full_name')
                        ->label('Occupant Involved')
                        ->placeholder('—'),

                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->placeholder('—'),

                    TextEntry::make('action_taken')
                        ->label('Action Taken')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),

            Section::make('Resolution')
                ->columns(2)
                ->visible(fn ($record) => $record->status === 'resolved')
                ->schema([
                    TextEntry::make('resolved_at')
                        ->label('Resolved At')
                        ->dateTime()
                        ->placeholder('—'),
                ]),
        ]);
    }
}

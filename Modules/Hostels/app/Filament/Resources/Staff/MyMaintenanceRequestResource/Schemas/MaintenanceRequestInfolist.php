<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MaintenanceRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('title')
                        ->columnSpanFull()
                        ->weight('bold'),

                    TextEntry::make('priority')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'urgent' => 'danger',
                            'high'   => 'warning',
                            'medium' => 'info',
                            'low'    => 'gray',
                            default  => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'open'        => 'warning',
                            'in_progress' => 'info',
                            'completed'   => 'success',
                            'cancelled'   => 'gray',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                    TextEntry::make('hostel.name')
                        ->label('Hostel'),

                    TextEntry::make('room.room_number')
                        ->label('Room')
                        ->placeholder('—'),

                    TextEntry::make('bed.bed_number')
                        ->label('Bed')
                        ->placeholder('—'),

                    TextEntry::make('reported_at')
                        ->label('Reported At')
                        ->dateTime()
                        ->placeholder('—'),

                    TextEntry::make('assignedToUser.name')
                        ->label('Assigned To')
                        ->placeholder('Not yet assigned'),

                    TextEntry::make('completed_at')
                        ->label('Completed At')
                        ->dateTime()
                        ->placeholder('—'),

                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),
        ]);
    }
}

<?php

namespace Modules\Hostels\Filament\Resources\IncidentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Hostels\Filament\Resources\IncidentResource;

class ViewIncident extends ViewRecord
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Incident Details')
                ->schema([
                    TextEntry::make('hostel.name')->label('Hostel'),
                    TextEntry::make('title')->label('Title'),
                    TextEntry::make('room.room_number')->label('Room')->placeholder('—'),
                    TextEntry::make('occupant.first_name')->label('Occupant')->placeholder('—'),
                    TextEntry::make('severity')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'critical' => 'danger',
                            'major'    => 'warning',
                            'minor'    => 'info',
                            default    => 'gray',
                        }),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'open'      => 'danger',
                            'escalated' => 'warning',
                            'resolved'  => 'success',
                            default     => 'gray',
                        }),
                ])
                ->columns(2),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->columnSpanFull(),
                    TextEntry::make('action_taken')->label('Action Taken')->columnSpanFull()->placeholder('No action recorded.'),
                ]),

            Section::make('Timeline')
                ->schema([
                    TextEntry::make('reportedByUser.name')->label('Reported By'),
                    TextEntry::make('reported_at')->label('Reported At')->dateTime(),
                    TextEntry::make('resolved_at')->label('Resolved At')->dateTime()->placeholder('Not resolved yet.'),
                ])
                ->columns(3),
        ]);
    }
}

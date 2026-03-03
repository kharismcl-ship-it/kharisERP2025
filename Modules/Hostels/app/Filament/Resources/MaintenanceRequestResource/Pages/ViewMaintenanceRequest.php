<?php

namespace Modules\Hostels\Filament\Resources\MaintenanceRequestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Hostels\Filament\Resources\MaintenanceRequestResource;

class ViewMaintenanceRequest extends ViewRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

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
            Section::make('Request Details')
                ->schema([
                    TextEntry::make('hostel.name')->label('Hostel'),
                    TextEntry::make('title')->label('Title'),
                    TextEntry::make('room.room_number')->label('Room')->placeholder('—'),
                    TextEntry::make('bed.bed_number')->label('Bed')->placeholder('—'),
                    TextEntry::make('priority')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'urgent' => 'danger',
                            'high'   => 'warning',
                            'medium' => 'info',
                            'low'    => 'gray',
                            default  => 'gray',
                        }),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'open'        => 'danger',
                            'in_progress' => 'warning',
                            'completed'   => 'success',
                            'cancelled'   => 'gray',
                            default       => 'gray',
                        }),
                ])
                ->columns(2),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->columnSpanFull(),
                ]),

            Section::make('Assignment & Timeline')
                ->schema([
                    TextEntry::make('reportedByUser.name')->label('Reported By User')->placeholder('—'),
                    TextEntry::make('reportedByHostelOccupant.first_name')->label('Reported By Occupant')->placeholder('—'),
                    TextEntry::make('assignedToUser.name')->label('Assigned To')->placeholder('Unassigned'),
                    TextEntry::make('reported_at')->label('Reported At')->dateTime(),
                    TextEntry::make('completed_at')->label('Completed At')->dateTime()->placeholder('Not completed yet.'),
                ])
                ->columns(2),
        ]);
    }
}

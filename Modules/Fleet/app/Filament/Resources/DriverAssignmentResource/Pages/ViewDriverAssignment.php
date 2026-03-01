<?php

namespace Modules\Fleet\Filament\Resources\DriverAssignmentResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Fleet\Filament\Resources\DriverAssignmentResource;

class ViewDriverAssignment extends ViewRecord
{
    protected static string $resource = DriverAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->is_active),

            Action::make('end_assignment')
                ->label('End Assignment')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->is_active)
                ->requiresConfirmation()
                ->modalHeading('End Driver Assignment')
                ->modalDescription('This will set the assignment end date to today. The driver will no longer be associated with this vehicle.')
                ->action(function () {
                    $this->record->update(['assigned_until' => now()->toDateString()]);
                    $this->refreshFormData(['assigned_until']);
                    Notification::make()->title('Assignment ended — driver unlinked from vehicle')->success()->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Assignment Details')
                ->description('Driver and vehicle assignment information')
                ->columns(3)
                ->schema([
                    TextEntry::make('vehicle.name')
                        ->label('Vehicle')
                        ->weight('bold')
                        ->icon('heroicon-o-truck'),

                    TextEntry::make('vehicle.plate')
                        ->label('Plate Number')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('user.name')
                        ->label('Assigned Driver')
                        ->weight('bold')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('assigned_from')
                        ->label('Assigned From')
                        ->date('d M Y'),

                    TextEntry::make('assigned_until')
                        ->label('Assigned Until')
                        ->date('d M Y')
                        ->placeholder('Ongoing — no end date set'),

                    IconEntry::make('is_primary')
                        ->label('Primary Driver')
                        ->boolean(),
                ]),

            Section::make('Status')
                ->columns(2)
                ->schema([
                    TextEntry::make('is_active')
                        ->label('Assignment Status')
                        ->getStateUsing(fn ($record) => $record->is_active ? 'Active' : 'Ended')
                        ->badge()
                        ->color(fn (string $state): string => $state === 'Active' ? 'success' : 'danger'),

                    TextEntry::make('vehicle.status')
                        ->label('Vehicle Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'            => 'success',
                            'inactive'          => 'gray',
                            'under_maintenance' => 'warning',
                            'retired'           => 'danger',
                            default             => 'gray',
                        }),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('No notes recorded'),
                ]),

            Section::make('Audit')
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('Created'),
                    TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                ]),
        ]);
    }
}
<?php

namespace Modules\Fleet\Filament\Resources\TripLogResource\Pages;

use EduardoRibeiroDev\FilamentLeaflet\Infolists\MapEntry;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Fleet\Filament\Resources\TripLogResource;

class ViewTripLog extends ViewRecord
{
    protected static string $resource = TripLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => ! in_array($this->record->status, ['completed', 'cancelled'])),

            Action::make('start_trip')
                ->label('Start Trip')
                ->icon('heroicon-o-play')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'planned')
                ->requiresConfirmation()
                ->modalHeading('Start Trip')
                ->modalDescription('This will mark the trip as In Progress.')
                ->action(function () {
                    $this->record->update(['status' => 'in_progress']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Trip started — status updated to In Progress')->warning()->send();
                }),

            Action::make('complete_trip')
                ->label('Complete Trip')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'in_progress')
                ->form([
                    TextInput::make('end_mileage')
                        ->label('End Mileage (km)')
                        ->numeric()
                        ->step(0.01)
                        ->required()
                        ->placeholder('Odometer reading at trip end'),
                    TimePicker::make('return_time')
                        ->label('Return Time')
                        ->nullable(),
                    Textarea::make('notes')
                        ->label('Trip Completion Notes')
                        ->rows(2)
                        ->nullable(),
                ])
                ->modalHeading('Complete Trip')
                ->action(function (array $data) {
                    $updates = ['status' => 'completed'];

                    if (! empty($data['end_mileage'])) {
                        $updates['end_mileage'] = $data['end_mileage'];
                        if ($this->record->start_mileage) {
                            $updates['distance_km'] = max(0, round($data['end_mileage'] - $this->record->start_mileage, 2));
                        }
                    }

                    if (! empty($data['return_time'])) {
                        $updates['return_time'] = $data['return_time'];
                    }

                    if (! empty($data['notes'])) {
                        $updates['notes'] = $data['notes'];
                    }

                    $this->record->update($updates);
                    $this->refreshFormData(['status', 'end_mileage', 'distance_km', 'return_time', 'notes']);
                    Notification::make()->title('Trip completed successfully')->success()->send();
                }),

            Action::make('cancel_trip')
                ->label('Cancel Trip')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => in_array($this->record->status, ['planned', 'in_progress']))
                ->requiresConfirmation()
                ->modalHeading('Cancel Trip')
                ->modalDescription('This trip will be marked as cancelled.')
                ->action(function () {
                    $this->record->update(['status' => 'cancelled']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Trip cancelled')->danger()->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Trip Overview')
                ->description('Core trip details and current status')
                ->columns(3)
                ->schema([
                    TextEntry::make('trip_reference')
                        ->label('Reference')
                        ->weight('bold')
                        ->copyable(),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'completed'   => 'success',
                            'in_progress' => 'warning',
                            'planned'     => 'info',
                            'cancelled'   => 'danger',
                            default       => 'gray',
                        }),

                    TextEntry::make('trip_date')
                        ->label('Trip Date')
                        ->date('d M Y'),

                    TextEntry::make('vehicle.name')
                        ->label('Vehicle')
                        ->weight('bold')
                        ->icon('heroicon-o-truck'),

                    TextEntry::make('vehicle.plate')
                        ->label('Plate Number')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('driver.name')
                        ->label('Driver')
                        ->placeholder('Unassigned'),
                ]),

            Section::make('Route Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('origin')
                        ->label('Origin / From')
                        ->icon('heroicon-o-map-pin'),

                    TextEntry::make('destination')
                        ->label('Destination / To')
                        ->icon('heroicon-o-map-pin'),

                    TextEntry::make('purpose')
                        ->label('Trip Purpose')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),

            Section::make('Route Map')
                ->icon('heroicon-o-map')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    MapEntry::make('origin_map')
                        ->label('Origin Location')
                        ->latitudeFieldName('origin_lat')
                        ->longitudeFieldName('origin_lng')
                        ->center(5.6037, -0.1870)
                        ->height(300)
                        ->zoom(13)
                        ->static()
                        ->fullscreenControl()
                        ->scaleControl()
                        ->columnSpanFull(),

                    MapEntry::make('destination_map')
                        ->label('Destination Location')
                        ->latitudeFieldName('destination_lat')
                        ->longitudeFieldName('destination_lng')
                        ->center(5.6037, -0.1870)
                        ->height(300)
                        ->zoom(13)
                        ->static()
                        ->fullscreenControl()
                        ->scaleControl()
                        ->columnSpanFull(),
                ]),

            Section::make('Mileage & Timing')
                ->columns(5)
                ->schema([
                    TextEntry::make('start_mileage')
                        ->label('Start Mileage')
                        ->numeric(decimalPlaces: 0)
                        ->suffix(' km')
                        ->placeholder('—'),

                    TextEntry::make('end_mileage')
                        ->label('End Mileage')
                        ->numeric(decimalPlaces: 0)
                        ->suffix(' km')
                        ->placeholder('—'),

                    TextEntry::make('distance_km')
                        ->label('Distance Covered')
                        ->numeric(decimalPlaces: 1)
                        ->suffix(' km')
                        ->weight('bold')
                        ->color('primary')
                        ->placeholder('—'),

                    TextEntry::make('departure_time')
                        ->label('Departure Time')
                        ->placeholder('—'),

                    TextEntry::make('return_time')
                        ->label('Return Time')
                        ->placeholder('—'),
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
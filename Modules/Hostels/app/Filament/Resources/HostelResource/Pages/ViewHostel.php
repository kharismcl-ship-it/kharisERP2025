<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\Pages;

use Dotswan\MapPicker\Infolists\MapEntry;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Hostels\Filament\Resources\HostelResource;
use Modules\Hostels\Models\Hostel;

class ViewHostel extends ViewRecord
{
    protected static string $resource = HostelResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hostel Information')
                    ->schema([
                        TextEntry::make('company.name')
                            ->label('Company'),
                        TextEntry::make('name')
                            ->label('Hostel Name'),
                        TextEntry::make('code')
                            ->label('Hostel Code'),
                        TextEntry::make('slug'),
                        TextEntry::make('description')
                            ->label('Hostel Description')
                            ->columnSpanFull(),
                        ImageEntry::make('photo')
                            ->label('Hostel Photo')
                            ->disk('public')
                            ->columnSpanFull(),
                        TextEntry::make('capacity')
                            ->formatStateUsing(fn (int $state): string => number_format($state)),
                        TextEntry::make('gender_policy')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'male' => 'info',
                                'female' => 'success',
                                'mixed' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('check_in_time_default')
                            ->label('Default Check-in Time')
                            ->time(),
                        TextEntry::make('check_out_time_default')
                            ->label('Default Check-out Time')
                            ->time(),
                        TextEntry::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Hostel Location')
                    ->description('Hostel location information')
                    ->collapsible(true)
                    ->schema([

                        TextEntry::make('country')
                            ->label('Country'),

                        TextEntry::make('region')
                            ->label('Region'),

                        TextEntry::make('city')
                            ->label('City'),

                        MapEntry::make('location')
                            ->label('Location')
                            ->columnSpanFull()
                            // Basic Configuration
                            ->defaultLocation(latitude: 5.6037, longitude: -0.1870)
                            ->draggable(false)
                            ->zoom(15)
                            ->minZoom(0)
                            ->maxZoom(28)
                            ->tilesUrl('https://tile.openstreetmap.de/{z}/{x}/{y}.png')
                            ->detectRetina(true)

                            // Marker Configuration
                            ->showMarker(true)
                            ->markerColor('#3b82f6')
                            ->markerIconAnchor([18, 36])

                            // Controls
                            ->showFullscreenControl(true)
                            ->showZoomControl(true)

                            // State Management
                            ->state(fn ($record) => [
                                'lat' => $record?->latitude,
                                'lng' => $record?->longitude,
                            ]),

                        TextEntry::make('latitude')
                            ->hiddenLabel()
                            ->hidden(),

                        TextEntry::make('longitude')
                            ->hiddenLabel()
                            ->hidden(),

                    ])
                    ->columns(2),

                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('blocks_count')
                            ->label('Blocks')
                            ->state(fn (Hostel $record): int => $record->blocks()->count()),
                        TextEntry::make('floors_count')
                            ->label('Floors')
                            ->state(fn (Hostel $record): int => $record->floors()->count()),
                        TextEntry::make('rooms_count')
                            ->label('Rooms')
                            ->state(fn (Hostel $record): int => $record->rooms()->count()),
                        TextEntry::make('beds_count')
                            ->label('Beds')
                            ->state(fn (Hostel $record): int => $record->beds()->count()),
                        TextEntry::make('hostel_occupants_count')
                            ->label('Hostel Occupants')
                            ->state(fn (Hostel $record): int => $record->hostelOccupants()->count()),
                        TextEntry::make('bookings_count')
                            ->label('Bookings')
                            ->state(fn (Hostel $record): int => $record->bookings()->count()),
                        TextEntry::make('maintenance_requests_count')
                            ->label('Maintenance Requests')
                            ->state(fn (Hostel $record): int => $record->maintenanceRequests()->count()),
                        TextEntry::make('incidents_count')
                            ->label('Incidents')
                            ->state(fn (Hostel $record): int => $record->incidents()->count()),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
            Actions\EditAction::make(),
        ];
    }
}

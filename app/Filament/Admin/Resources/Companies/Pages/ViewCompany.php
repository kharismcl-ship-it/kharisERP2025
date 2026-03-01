<?php

namespace App\Filament\Admin\Resources\Companies\Pages;

use App\Enums\Countries;
use App\Models\Company;
use Dotswan\MapPicker\Infolists\MapEntry;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewCompany extends ViewRecord
{
    protected static string $resource = \App\Filament\Admin\Resources\Companies\CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil')
                ->label('Edit Company'),
            // Actions\Action::make('switch')
            //     ->icon('heroicon-o-arrow-right-circle')
            //     ->label('Switch to Company')
            //     ->color('success')
            //     ->url(fn (Company $record) => route('companies.switch', [
            //         'slug' => $record->slug,
            //         'to' => '/dashboard',
            //     ])),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->label('Delete Company'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    ImageEntry::make('company_logo')
                        ->label('Company Logo')
                        ->disk('public')
                        ->width('150px')
                        ->height('150px')
                        ->alignCenter()
                        ->extraAttributes([
                            'class' => 'rounded-lg shadow-md border-2 border-gray-200',
                        ])
                        ->columnSpanFull(),

                    TextEntry::make('name')
                        ->label('Company Name')
                        ->size('lg')
                        ->weight('bold')
                        ->icon('heroicon-o-building-office-2'),

                    TextEntry::make('slug')
                        ->label('Company Slug')
                        ->icon('heroicon-o-link')
                        ->color('gray'),

                    TextEntry::make('type')
                        ->label('Company Type')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => ucfirst($state))
                        ->color(fn (string $state): string => match ($state) {
                            'main' => 'info',
                            'subsidiary' => 'success',
                            default => 'gray',
                        }),

                    TextEntry::make('parentCompany.name')
                        ->label('Parent Company')
                        ->icon('heroicon-o-building-office')
                        ->visible(fn (Company $record) => $record->type === 'subsidiary'),

                    IconEntry::make('is_active')
                        ->label('Status')
                        ->boolean()
                        ->trueIcon('heroicon-o-check-circle')
                        ->falseIcon('heroicon-o-x-circle')
                        ->trueColor('success')
                        ->falseColor('danger'),

                ])->columns(2),

                Section::make('Service Information')
                    ->icon('heroicon-o-cog')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('company_service_type')
                                ->label('Primary Service Type')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => ucfirst($state))
                                ->color('primary'),

                            TextEntry::make('company_service_description')
                                ->label('Service Description')
                                ->html()
                                ->columnSpanFull(),
                        ]),
                ]),

                Section::make('Contact Information')
                    ->icon('heroicon-o-phone')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('company_country')
                                ->label('Country')
                                ->formatStateUsing(fn ($state) => Countries::tryFrom($state)?->value ?? $state)
                                ->icon('heroicon-o-flag'),

                            TextEntry::make('company_city')
                                ->label('City')
                                ->icon('heroicon-o-map-pin'),

                            TextEntry::make('company_address')
                                ->label('Address')
                                ->icon('heroicon-o-home')
                                ->columnSpan(2),

                            TextEntry::make('company_phone')
                                ->label('Phone')
                                ->icon('heroicon-o-phone')
                                ->url(fn ($state) => "tel:{$state}"),

                            TextEntry::make('company_email')
                                ->label('Email')
                                ->icon('heroicon-o-envelope')
                                ->url(fn ($state) => "mailto:{$state}"),

                            TextEntry::make('company_website')
                                ->label('Website')
                                ->icon('heroicon-o-globe-alt')
                                ->url(fn ($state) => $state ? (str_starts_with($state, 'http') ? $state : "https://{$state}") : null)
                                ->openUrlInNewTab()
                                ->columnSpan(2),
                        ]),
                ]),

                Section::make('Location Details')
                    ->icon('heroicon-o-map')
                    ->collapsible()
                    ->columns('2')
                    ->schema([

                        MapEntry::make('company_location')
                            // Basic Configuration
                            ->draggable(false) // Usually false for infolist view
                            ->zoom(15)
                            ->minZoom(0)
                            ->maxZoom(28)
                            ->tilesUrl('https://tile.openstreetmap.de/{z}/{x}/{y}.png')
                            ->detectRetina(true)

                            // Marker Configuration
                            ->showMarker(true)
                            ->markerColor('#22c55eff')
                            ->markerIconAnchor([18, 36])

                            // Controls
                            ->showFullscreenControl(true)
                            ->showZoomControl(true)

                            // GeoMan Integration (if needed for viewing)
                            ->geoMan(true)
                            ->geoManEditable(false) // Usually false for infolist view
                            ->geoManPosition('topleft')
                            ->drawCircleMarker(true)
                            ->drawMarker(true)
                            ->drawPolygon(true)
                            ->drawPolyline(true)
                            ->drawCircle(true)
                            ->drawRectangle(true)
                            ->drawText(true)

                            // State Management
                            ->state(fn ($record) => [
                                'lat' => $record?->company_latitude,
                                'lng' => $record?->company_longitude,
                                'geojson' => $record?->geojson ? json_decode($record->geojson) : null,
                            ])->columnSpanFull(),

                        TextEntry::make('company_latitude')
                            ->label('Latitude')
                            ->icon('heroicon-o-map-pin'),

                        TextEntry::make('company_longitude')
                            ->label('Longitude')
                            ->icon('heroicon-o-map-pin'),

                        TextEntry::make('company_ghanapostgps')
                            ->label('Ghana Post GPS')
                            ->icon('heroicon-o-map'),

                ]),

                Section::make('Tenant Information')
                    ->icon('heroicon-o-users')
                    ->collapsible()
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('users')
                            ->label('Tenant Users')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope'),
                            ])
                            ->columns(2)
                            ->grid(2),
                ]),

                Section::make('System Information')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('created_at')
                                ->label('Created')
                                ->dateTime()
                                ->since()
                                ->icon('heroicon-o-calendar'),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime()
                                ->since()
                                ->icon('heroicon-o-clock'),
                        ]),
                ]),
            ]);
    }
}

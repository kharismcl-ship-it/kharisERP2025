<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Fleet\Filament\Resources\VehicleResource;
use Modules\Fleet\Models\FuelLog;
use Modules\Fleet\Models\MaintenanceRecord;
use Modules\Fleet\Models\TripLog;
use Modules\Fleet\Services\FleetService;

class ViewVehicle extends ViewRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Fleet Summary')
                ->description('Activity KPIs for this vehicle')
                ->columns(5)
                ->schema([
                    TextEntry::make('total_maintenance_cost_ytd')
                        ->label('Maintenance Cost (YTD)')
                        ->getStateUsing(fn ($record) =>
                            'GHS ' . number_format(
                                MaintenanceRecord::where('vehicle_id', $record->id)
                                    ->where('status', 'completed')
                                    ->whereYear('service_date', now()->year)
                                    ->sum('cost'),
                                2
                            )
                        )
                        ->weight('bold')
                        ->color('warning'),

                    TextEntry::make('total_fuel_cost_ytd')
                        ->label('Fuel Cost (YTD)')
                        ->getStateUsing(fn ($record) =>
                            'GHS ' . number_format(
                                FuelLog::where('vehicle_id', $record->id)
                                    ->whereYear('fill_date', now()->year)
                                    ->sum('total_cost'),
                                2
                            )
                        )
                        ->weight('bold')
                        ->color('info'),

                    TextEntry::make('total_trips_ytd')
                        ->label('Trips (YTD)')
                        ->getStateUsing(fn ($record) =>
                            TripLog::where('vehicle_id', $record->id)
                                ->whereYear('trip_date', now()->year)
                                ->count()
                        )
                        ->suffix(' trips'),

                    TextEntry::make('total_distance_ytd')
                        ->label('Distance (YTD)')
                        ->getStateUsing(fn ($record) =>
                            number_format(
                                TripLog::where('vehicle_id', $record->id)
                                    ->whereYear('trip_date', now()->year)
                                    ->where('status', 'completed')
                                    ->sum('distance_km'),
                                0
                            ) . ' km'
                        ),

                    TextEntry::make('health_score')
                        ->label('Health Score')
                        ->getStateUsing(fn ($record) => app(FleetService::class)->healthScore($record) . '%')
                        ->color(function ($record) {
                            $score = app(FleetService::class)->healthScore($record);
                            return $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                        })
                        ->weight('bold'),
                ]),

            Section::make('Vehicle Identity')
                ->description('Registration and classification details')
                ->columns(3)
                ->schema([
                    TextEntry::make('name')
                        ->label('Vehicle Name')
                        ->weight('bold'),

                    TextEntry::make('plate')
                        ->label('Plate Number')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'            => 'success',
                            'inactive'          => 'gray',
                            'under_maintenance' => 'warning',
                            'retired'           => 'danger',
                            default             => 'gray',
                        }),

                    TextEntry::make('make')->label('Make'),
                    TextEntry::make('model')->label('Model'),
                    TextEntry::make('year')->label('Year'),

                    TextEntry::make('type')
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('color')->label('Color')->placeholder('—'),

                    TextEntry::make('fuel_type')
                        ->label('Fuel Type')
                        ->badge()
                        ->color('success'),
                ]),

            Section::make('Technical Details')
                ->columns(3)
                ->collapsible()
                ->schema([
                    TextEntry::make('chassis_number')
                        ->label('Chassis Number')
                        ->placeholder('—')
                        ->copyable(),

                    TextEntry::make('engine_number')
                        ->label('Engine Number')
                        ->placeholder('—')
                        ->copyable(),

                    TextEntry::make('capacity')
                        ->label('Capacity')
                        ->placeholder('—'),

                    TextEntry::make('current_mileage')
                        ->label('Current Mileage')
                        ->numeric(decimalPlaces: 0)
                        ->suffix(' km'),

                    TextEntry::make('currentDriver.user.name')
                        ->label('Primary Driver')
                        ->placeholder('Unassigned'),
                ]),

            Section::make('Purchase Information')
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('purchase_date')
                        ->label('Purchase Date')
                        ->date('d M Y')
                        ->placeholder('—'),

                    TextEntry::make('purchase_price')
                        ->label('Purchase Price')
                        ->money('GHS')
                        ->placeholder('—'),

                    TextEntry::make('description')
                        ->columnSpanFull()
                        ->placeholder('No description'),
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

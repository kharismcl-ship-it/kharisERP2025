<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmWeatherAlertResource\Pages;
use Modules\Farms\Models\FarmWeatherAlert;

class FarmWeatherAlertResource extends Resource
{
    protected static ?string $model = FarmWeatherAlert::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static string|\UnitEnum|null $navigationGroup = 'Precision Agriculture';

    protected static ?string $navigationLabel = 'Weather Alerts';

    protected static ?int $navigationSort = 42;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->sortable()->searchable(),
                TextColumn::make('alert_type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'frost'             => 'info',
                        'heat_stress'       => 'danger',
                        'heavy_rain'        => 'primary',
                        'drought'           => 'warning',
                        'high_wind'         => 'gray',
                        'spray_window_open' => 'success',
                        'spray_window_closed' => 'warning',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'critical' => 'danger',
                        'warning'  => 'warning',
                        'info'     => 'info',
                        default    => 'gray',
                    }),
                TextColumn::make('title')->searchable(),
                TextColumn::make('triggered_at')->label('Triggered')->dateTime()->sortable(),
                ToggleColumn::make('is_read')->label('Read'),
            ])
            ->filters([
                SelectFilter::make('alert_type')->options([
                    'frost'               => 'Frost',
                    'heat_stress'         => 'Heat Stress',
                    'heavy_rain'          => 'Heavy Rain',
                    'drought'             => 'Drought',
                    'high_wind'           => 'High Wind',
                    'disease_pressure'    => 'Disease Pressure',
                    'spray_window_open'   => 'Spray Window Open',
                    'spray_window_closed' => 'Spray Window Closed',
                ]),
                SelectFilter::make('severity')->options([
                    'critical' => 'Critical', 'warning' => 'Warning', 'info' => 'Info',
                ]),
                TernaryFilter::make('is_read'),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('mark_all_read')
                    ->label('Mark All Read')
                    ->icon('heroicon-o-check-circle')
                    ->action(function () {
                        FarmWeatherAlert::where('company_id', Filament::getTenant()?->id)
                            ->where('is_read', false)
                            ->update(['is_read' => true]);
                    })
                    ->requiresConfirmation(),
            ])
            ->defaultSort('triggered_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarmWeatherAlerts::route('/'),
        ];
    }
}
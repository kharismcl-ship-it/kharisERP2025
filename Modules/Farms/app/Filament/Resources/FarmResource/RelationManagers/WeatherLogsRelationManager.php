<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\FarmWeatherLog;

class WeatherLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'weatherLogs';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('log_date')->required()->default(now()),
            Select::make('weather_condition')
                ->options(array_combine(
                    FarmWeatherLog::CONDITIONS,
                    array_map(fn ($c) => str_replace('_', ' ', ucfirst($c)), FarmWeatherLog::CONDITIONS)
                ))->nullable(),
            TextInput::make('rainfall_mm')->label('Rainfall (mm)')->numeric()->step(0.01),
            TextInput::make('min_temp_c')->label('Min Temp °C')->numeric()->step(0.01),
            TextInput::make('max_temp_c')->label('Max Temp °C')->numeric()->step(0.01),
            TextInput::make('humidity_pct')->label('Humidity %')->integer(),
            TextInput::make('wind_speed_kmh')->label('Wind km/h')->numeric()->step(0.01),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_date')->date()->sortable(),
                TextColumn::make('weather_condition')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'sunny' => 'warning', 'rainy' => 'info', 'stormy' => 'danger', default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ? str_replace('_', ' ', ucfirst($state)) : '—'),
                TextColumn::make('rainfall_mm')->label('Rain mm'),
                TextColumn::make('min_temp_c')->label('Min °C'),
                TextColumn::make('max_temp_c')->label('Max °C'),
                TextColumn::make('humidity_pct')->label('Humidity %'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('log_date', 'desc');
    }
}
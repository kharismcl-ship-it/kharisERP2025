<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmWeatherLogResource\Pages;
use Modules\Farms\Models\FarmWeatherLog;

class FarmWeatherLogResource extends Resource
{
    protected static ?string $model = FarmWeatherLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cloud';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 14;

    protected static ?string $navigationLabel = 'Weather Logs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Weather Entry')
                ->columns(3)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('log_date')->required()->default(now()),

                    Select::make('weather_condition')
                        ->options(array_combine(
                            FarmWeatherLog::CONDITIONS,
                            array_map(fn ($c) => str_replace('_', ' ', ucfirst($c)), FarmWeatherLog::CONDITIONS)
                        ))
                        ->nullable(),

                    TextInput::make('rainfall_mm')->label('Rainfall (mm)')->numeric()->step(0.01),
                    TextInput::make('min_temp_c')->label('Min Temp (°C)')->numeric()->step(0.01),
                    TextInput::make('max_temp_c')->label('Max Temp (°C)')->numeric()->step(0.01),
                    TextInput::make('humidity_pct')->label('Humidity (%)')->integer()->minValue(0)->maxValue(100),
                    TextInput::make('wind_speed_kmh')->label('Wind Speed (km/h)')->numeric()->step(0.01),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([Textarea::make('notes')->rows(2)->columnSpanFull()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('log_date')->date()->sortable(),

                TextColumn::make('weather_condition')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'sunny'         => 'warning',
                        'rainy'         => 'info',
                        'stormy'        => 'danger',
                        'partly_cloudy' => 'primary',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ? str_replace('_', ' ', ucfirst($state)) : '—'),

                TextColumn::make('rainfall_mm')->label('Rain (mm)'),
                TextColumn::make('min_temp_c')->label('Min °C'),
                TextColumn::make('max_temp_c')->label('Max °C'),
                TextColumn::make('humidity_pct')->label('Humidity %'),
                TextColumn::make('wind_speed_kmh')->label('Wind km/h')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('weather_condition')
                    ->options(array_combine(
                        FarmWeatherLog::CONDITIONS,
                        array_map(fn ($c) => str_replace('_', ' ', ucfirst($c)), FarmWeatherLog::CONDITIONS)
                    )),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('log_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmWeatherLogs::route('/'),
            'create' => Pages\CreateFarmWeatherLog::route('/create'),
            'view'   => Pages\ViewFarmWeatherLog::route('/{record}'),
            'edit'   => Pages\EditFarmWeatherLog::route('/{record}/edit'),
        ];
    }
}
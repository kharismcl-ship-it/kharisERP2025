<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Facades\Filament;
use Modules\Farms\Filament\Resources\FarmNdviResource\Pages;
use Modules\Farms\Models\FarmNdviRecord;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmPlot;

class FarmNdviResource extends Resource
{
    protected static ?string $model = FarmNdviRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    protected static string|\UnitEnum|null $navigationGroup = 'Precision Agriculture';

    protected static ?string $navigationLabel = 'NDVI Records';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('NDVI Reading')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),

                    Select::make('farm_plot_id')
                        ->label('Plot (optional)')
                        ->options(fn (\Filament\Forms\Get $get) => FarmPlot::where('farm_id', $get('farm_id'))->pluck('name', 'id'))
                        ->nullable(),

                    DatePicker::make('recorded_date')->required()->default(now()),

                    TextInput::make('ndvi_value')
                        ->label('NDVI Value')
                        ->numeric()
                        ->step(0.0001)
                        ->minValue(-1)
                        ->maxValue(1)
                        ->helperText('Range: -1.0 to 1.0 | Healthy crops: 0.4+')
                        ->required(),

                    TextInput::make('ndvi_min')->label('NDVI Min')->numeric()->step(0.0001)->nullable(),
                    TextInput::make('ndvi_max')->label('NDVI Max')->numeric()->step(0.0001)->nullable(),

                    Select::make('source')
                        ->options([
                            'sentinel2' => 'Sentinel-2',
                            'planet'    => 'Planet',
                            'manual'    => 'Manual',
                            'drone'     => 'Drone',
                        ])
                        ->default('manual'),

                    TextInput::make('cloud_cover_pct')->label('Cloud Cover (%)')->numeric()->step(0.01)->nullable(),

                    Toggle::make('stress_detected')->label('Stress Detected'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->sortable()->searchable(),
                TextColumn::make('farmPlot.name')->label('Plot')->toggleable(),
                TextColumn::make('recorded_date')->date()->sortable(),
                TextColumn::make('ndvi_value')
                    ->label('NDVI')
                    ->badge()
                    ->color(fn (?float $state): string => match(true) {
                        $state === null    => 'gray',
                        $state >= 0.5     => 'success',
                        $state >= 0.3     => 'warning',
                        default           => 'danger',
                    })
                    ->formatStateUsing(fn (?float $state): string => $state !== null ? number_format($state, 4) : '—'),
                TextColumn::make('health_label')
                    ->label('Health')
                    ->getStateUsing(fn (FarmNdviRecord $record): string => $record->healthLabel()),
                TextColumn::make('source')->badge()->color('info'),
                IconColumn::make('stress_detected')->label('Stress')->boolean(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('source')->options([
                    'sentinel2' => 'Sentinel-2', 'planet' => 'Planet', 'manual' => 'Manual', 'drone' => 'Drone',
                ]),
                TernaryFilter::make('stress_detected'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('recorded_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmNdviRecords::route('/'),
            'create' => Pages\CreateFarmNdviRecord::route('/create'),
            'edit'   => Pages\EditFarmNdviRecord::route('/{record}/edit'),
        ];
    }
}

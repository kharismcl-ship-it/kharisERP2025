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
use Modules\Farms\Filament\Resources\CropCycleResource\Pages;
use Modules\Farms\Filament\Resources\CropCycleResource\RelationManagers;
use Modules\Farms\Models\CropCycle;

class CropCycleResource extends Resource
{
    protected static ?string $model = CropCycle::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Crop Cycles';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Crop Identity')
                ->columns(3)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('farm_plot_id')
                        ->label('Plot')
                        ->relationship('plot', 'name')
                        ->searchable()
                        ->nullable(),

                    TextInput::make('crop_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('variety')
                        ->maxLength(255)
                        ->placeholder('e.g. Hybrid, Local'),

                    TextInput::make('season')
                        ->maxLength(100)
                        ->placeholder('Major, Minor, Dry Season'),

                    Select::make('status')
                        ->options(array_combine(CropCycle::STATUSES, array_map('ucfirst', CropCycle::STATUSES)))
                        ->default('growing')
                        ->required(),
                ]),

            Section::make('Planting & Harvest Dates')
                ->columns(3)
                ->schema([
                    DatePicker::make('planting_date')->required(),
                    DatePicker::make('expected_harvest_date')->label('Expected Harvest'),
                    DatePicker::make('actual_harvest_date')->label('Actual Harvest'),
                ]),

            Section::make('Area & Yield Target')
                ->columns(4)
                ->schema([
                    TextInput::make('planted_area')
                        ->label('Planted Area')
                        ->numeric()
                        ->step(0.0001),

                    Select::make('planted_area_unit')
                        ->options(['acres' => 'Acres', 'hectares' => 'Hectares'])
                        ->default('acres'),

                    TextInput::make('expected_yield')
                        ->label('Expected Yield')
                        ->numeric()
                        ->step(0.001),

                    TextInput::make('yield_unit')
                        ->label('Yield Unit')
                        ->maxLength(50)
                        ->placeholder('kg, bags, tonnes'),
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
                TextColumn::make('crop_name')
                    ->label('Crop')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('plot.name')
                    ->label('Plot')
                    ->placeholder('—'),

                TextColumn::make('season')
                    ->placeholder('—'),

                TextColumn::make('planting_date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('expected_harvest_date')
                    ->date('d M Y')
                    ->label('Expected Harvest')
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : null),

                TextColumn::make('planted_area')
                    ->label('Area')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state ? number_format($state, 2) . ' ' . $record->planted_area_unit : '—'
                    ),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'growing'   => 'info',
                        'harvested' => 'success',
                        'preparing' => 'gray',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(CropCycle::STATUSES, array_map('ucfirst', CropCycle::STATUSES))),
                SelectFilter::make('farm_id')
                    ->label('Farm')
                    ->relationship('farm', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('planting_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HarvestRecordsRelationManager::class,
            RelationManagers\FarmExpensesRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
            RelationManagers\InputApplicationsRelationManager::class,
            RelationManagers\ScoutingRecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCropCycles::route('/'),
            'create' => Pages\CreateCropCycle::route('/create'),
            'view'   => Pages\ViewCropCycle::route('/{record}'),
            'edit'   => Pages\EditCropCycle::route('/{record}/edit'),
        ];
    }
}
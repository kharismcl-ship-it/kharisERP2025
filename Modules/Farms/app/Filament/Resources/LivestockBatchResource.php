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
use Modules\Farms\Filament\Resources\LivestockBatchResource\Pages;
use Modules\Farms\Models\LivestockBatch;

class LivestockBatchResource extends Resource
{
    protected static ?string $model = LivestockBatch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Livestock';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Batch Identity')
                ->columns(3)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('animal_type')
                        ->options(array_combine(LivestockBatch::ANIMAL_TYPES, array_map('ucfirst', LivestockBatch::ANIMAL_TYPES)))
                        ->required(),

                    TextInput::make('breed')
                        ->maxLength(255)
                        ->placeholder('e.g. Friesian, Broiler'),
                ]),

            Section::make('Count & Acquisition')
                ->columns(3)
                ->schema([
                    DatePicker::make('acquisition_date')->required(),

                    TextInput::make('initial_count')
                        ->label('Initial Count')
                        ->required()
                        ->numeric()
                        ->minValue(1),

                    TextInput::make('current_count')
                        ->label('Current Count')
                        ->required()
                        ->numeric()
                        ->minValue(0),

                    TextInput::make('acquisition_cost')
                        ->label('Acquisition Cost (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01),

                    Select::make('status')
                        ->options(array_combine(LivestockBatch::STATUSES, array_map('ucfirst', LivestockBatch::STATUSES)))
                        ->default('active'),
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
                TextColumn::make('batch_reference')
                    ->label('Reference')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('animal_type')
                    ->label('Type')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('breed')
                    ->placeholder('—'),

                TextColumn::make('farm.name')
                    ->label('Farm')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('acquisition_date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('current_count')
                    ->label('Count')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('mortality_rate')
                    ->label('Mortality %')
                    ->getStateUsing(fn ($record) => $record->getMortalityRateAttribute() . '%')
                    ->color(fn ($record) => $record->getMortalityRateAttribute() > 10 ? 'danger' : 'success'),

                TextColumn::make('acquisition_cost')
                    ->money('GHS')
                    ->label('Acq. Cost'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'      => 'success',
                        'sold'        => 'info',
                        'slaughtered' => 'warning',
                        'deceased'    => 'danger',
                        default       => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('animal_type')
                    ->options(array_combine(LivestockBatch::ANIMAL_TYPES, array_map('ucfirst', LivestockBatch::ANIMAL_TYPES))),
                SelectFilter::make('status')
                    ->options(array_combine(LivestockBatch::STATUSES, array_map('ucfirst', LivestockBatch::STATUSES))),
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
            ->defaultSort('acquisition_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            \Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers\HealthRecordsRelationManager::class,
            \Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers\WeightRecordsRelationManager::class,
            \Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers\FeedRecordsRelationManager::class,
            \Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers\MortalityLogsRelationManager::class,
            \Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers\LivestockEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLivestockBatches::route('/'),
            'create' => Pages\CreateLivestockBatch::route('/create'),
            'view'   => Pages\ViewLivestockBatch::route('/{record}'),
            'edit'   => Pages\EditLivestockBatch::route('/{record}/edit'),
        ];
    }
}
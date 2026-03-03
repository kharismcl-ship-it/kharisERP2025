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
use Modules\Farms\Filament\Resources\FarmSeasonResource\Pages;
use Modules\Farms\Filament\Resources\FarmSeasonResource\RelationManagers;
use Modules\Farms\Models\FarmSeason;

class FarmSeasonResource extends Resource
{
    protected static ?string $model = FarmSeason::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Farm Seasons';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Season Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('season_year')
                        ->label('Season Year')
                        ->numeric()
                        ->required()
                        ->default(now()->year)
                        ->minValue(2000)
                        ->maxValue(2100),

                    Select::make('status')
                        ->options([
                            'planning'  => 'Planning',
                            'active'    => 'Active',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('planning')
                        ->required(),

                    DatePicker::make('start_date')->required(),

                    DatePicker::make('end_date')->nullable(),

                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Targets & Budget')
                ->collapsible()
                ->columns(2)
                ->schema([
                    TextInput::make('target_yield')
                        ->label('Target Yield')
                        ->numeric()
                        ->step(0.001)
                        ->nullable(),

                    TextInput::make('yield_unit')
                        ->label('Yield Unit')
                        ->maxLength(50)
                        ->nullable(),

                    TextInput::make('total_budget')
                        ->label('Total Budget (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->nullable(),

                    TextInput::make('actual_cost')
                        ->label('Actual Cost (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->nullable(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('season_year')->label('Year')->sortable(),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('end_date')->date()->placeholder('—'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planning'  => 'gray',
                        'active'    => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('target_yield')
                    ->label('Target Yield')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state ? number_format($state, 2) . ' ' . $record->yield_unit : '—'
                    ),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'planning'  => 'Planning',
                    'active'    => 'Active',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('season_year', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FarmMilestonesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmSeasons::route('/'),
            'create' => Pages\CreateFarmSeason::route('/create'),
            'view'   => Pages\ViewFarmSeason::route('/{record}'),
            'edit'   => Pages\EditFarmSeason::route('/{record}/edit'),
        ];
    }
}

<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmRotationPlanResource\Pages;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmPlot;
use Modules\Farms\Models\FarmRotationPlan;
use Filament\Facades\Filament;

class FarmRotationPlanResource extends Resource
{
    protected static ?string $model = FarmRotationPlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'Crop Management';

    protected static ?string $navigationLabel = 'Rotation Plans';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plan Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->required(),

                    Select::make('farm_plot_id')
                        ->label('Plot')
                        ->options(fn (Get $get) => FarmPlot::where('farm_id', $get('farm_id'))->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('plan_name')->required()->maxLength(255),

                    TextInput::make('start_season')
                        ->label('Start Season')
                        ->placeholder('e.g. 2025 Season A')
                        ->required()
                        ->maxLength(100),

                    Select::make('total_years')
                        ->label('Total Years')
                        ->options(collect(range(2, 7))->mapWithKeys(fn ($y) => [$y => "{$y} years"]))
                        ->default(3)
                        ->required(),

                    Toggle::make('is_active')->default(true)->inline(false),
                ]),

            Section::make('Rotation Sequence')
                ->schema([
                    Repeater::make('rotation_sequence')
                        ->schema([
                            TextInput::make('year')->label('Year #')->numeric()->required(),
                            TextInput::make('season')->placeholder('e.g. A, B, Wet, Dry'),
                            TextInput::make('crop_name')->label('Crop')->required(),
                            TextInput::make('notes')->placeholder('Optional notes'),
                        ])
                        ->columns(4)
                        ->addActionLabel('Add Year')
                        ->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('nitrogen_balance_notes')
                        ->label('Nitrogen Balance Notes')
                        ->rows(2)
                        ->placeholder('Notes on nitrogen-fixing crops in the rotation')
                        ->columnSpanFull(),

                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('farmPlot.name')->label('Plot')->placeholder('—'),
                TextColumn::make('plan_name')->searchable(),
                TextColumn::make('start_season')->label('Start Season'),
                TextColumn::make('total_years')->label('Years')->suffix(' yrs'),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmRotationPlans::route('/'),
            'create' => Pages\CreateFarmRotationPlan::route('/create'),
            'edit'   => Pages\EditFarmRotationPlan::route('/{record}/edit'),
        ];
    }
}
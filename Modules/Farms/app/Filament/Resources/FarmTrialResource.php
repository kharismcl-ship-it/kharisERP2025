<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Modules\Farms\Models\FarmTrial;
use Modules\Farms\Models\Farm;
use Filament\Facades\Filament;

class FarmTrialResource extends Resource
{
    protected static ?string $model = FarmTrial::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static string|\UnitEnum|null $navigationGroup = 'Crop Management';
    protected static ?string $navigationLabel = 'Agronomic Trials';
    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Trial Details')->schema([
                Grid::make(2)->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('trial_name')
                        ->required()
                        ->maxLength(255),
                    Select::make('trial_type')
                        ->options([
                            'variety_comparison'  => 'Variety Comparison',
                            'input_comparison'    => 'Input Comparison',
                            'practice_comparison' => 'Practice Comparison',
                            'other'               => 'Other',
                        ])
                        ->required(),
                    TextInput::make('crop_name'),
                    DatePicker::make('start_date')->required(),
                    DatePicker::make('end_date'),
                    Select::make('status')
                        ->options([
                            'planned'   => 'Planned',
                            'active'    => 'Active',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('planned'),
                    TextInput::make('conducted_by'),
                ]),
                Textarea::make('hypothesis')->columnSpanFull(),
                Textarea::make('objective')->columnSpanFull(),
                Textarea::make('methodology')->columnSpanFull(),
                Textarea::make('conclusion')
                    ->columnSpanFull()
                    ->helperText('Fill in when trial is completed'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trial_name')->searchable()->sortable(),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('trial_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'variety_comparison'  => 'blue',
                        'input_comparison'    => 'success',
                        'practice_comparison' => 'purple',
                        default               => 'gray',
                    }),
                TextColumn::make('crop_name'),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planned'   => 'gray',
                        'active'    => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('trialPlots_count')
                    ->counts('trialPlots')
                    ->label('Treatments'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Farms\Filament\Resources\FarmTrialResource\Pages\ListFarmTrials::route('/'),
            'create' => \Modules\Farms\Filament\Resources\FarmTrialResource\Pages\CreateFarmTrial::route('/create'),
            'edit'   => \Modules\Farms\Filament\Resources\FarmTrialResource\Pages\EditFarmTrial::route('/{record}/edit'),
        ];
    }
}
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
use Modules\Farms\Filament\Resources\SoilTestRecordResource\Pages;
use Modules\Farms\Models\SoilTestRecord;

class SoilTestRecordResource extends Resource
{
    protected static ?string $model = SoilTestRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 15;

    protected static ?string $navigationLabel = 'Soil Tests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Test Details')
                ->columns(3)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('farm_plot_id')
                        ->label('Plot (optional)')
                        ->relationship('plot', 'name')
                        ->searchable()
                        ->nullable(),

                    DatePicker::make('test_date')->required()->default(now()),
                    TextInput::make('tested_by')->maxLength(150),
                    TextInput::make('lab_reference')->maxLength(100),

                    Select::make('texture')
                        ->options(array_combine(
                            SoilTestRecord::TEXTURES,
                            array_map(fn ($t) => str_replace('_', ' ', ucfirst($t)), SoilTestRecord::TEXTURES)
                        ))
                        ->nullable(),
                ]),

            Section::make('Nutrient Results')
                ->columns(3)
                ->schema([
                    TextInput::make('ph_level')->label('pH Level')->numeric()->step(0.01)->minValue(0)->maxValue(14),
                    TextInput::make('nitrogen_pct')->label('Nitrogen (%)')->numeric()->step(0.001),
                    TextInput::make('phosphorus_ppm')->label('Phosphorus (ppm)')->numeric()->step(0.001),
                    TextInput::make('potassium_ppm')->label('Potassium (ppm)')->numeric()->step(0.001),
                    TextInput::make('organic_matter_pct')->label('Organic Matter (%)')->numeric()->step(0.01),
                ]),

            Section::make('Recommendations & Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('recommendations')->rows(3)->columnSpanFull(),
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('plot.name')->label('Plot')->toggleable(),
                TextColumn::make('test_date')->date()->sortable(),
                TextColumn::make('tested_by')->toggleable(),
                TextColumn::make('ph_level')->label('pH'),
                TextColumn::make('nitrogen_pct')->label('N%'),
                TextColumn::make('phosphorus_ppm')->label('P ppm'),
                TextColumn::make('potassium_ppm')->label('K ppm'),
                TextColumn::make('organic_matter_pct')->label('OM%'),
                TextColumn::make('texture')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ? str_replace('_', ' ', ucfirst($state)) : '—'),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('texture')->options(
                    array_combine(
                        SoilTestRecord::TEXTURES,
                        array_map(fn ($t) => str_replace('_', ' ', ucfirst($t)), SoilTestRecord::TEXTURES)
                    )
                ),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('test_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSoilTestRecords::route('/'),
            'create' => Pages\CreateSoilTestRecord::route('/create'),
            'view'   => Pages\ViewSoilTestRecord::route('/{record}'),
            'edit'   => Pages\EditSoilTestRecord::route('/{record}/edit'),
        ];
    }
}
<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
use Modules\Farms\Filament\Resources\CropVarietyResource\Pages;
use Modules\Farms\Models\CropVariety;

class CropVarietyResource extends Resource
{
    protected static ?string $model = CropVariety::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 16;

    protected static ?string $navigationLabel = 'Crop Varieties';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Variety Identity')
                ->columns(3)
                ->schema([
                    TextInput::make('crop_name')
                        ->label('Crop (e.g. Maize, Tomato)')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('variety_name')
                        ->label('Variety (e.g. Pioneer 30B74)')
                        ->required()
                        ->maxLength(150),

                    TextInput::make('seed_supplier')->maxLength(150),

                    TextInput::make('planting_season')
                        ->label('Season (Major, Minor, Dry)')
                        ->maxLength(50),

                    TextInput::make('growing_period_days')
                        ->label('Growing Period (days)')
                        ->integer()
                        ->minValue(1),

                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ]),

            Section::make('Yield & Seeding')
                ->columns(3)
                ->schema([
                    TextInput::make('typical_yield_per_acre')
                        ->label('Typical Yield / Acre')
                        ->numeric()
                        ->step(0.001),
                    TextInput::make('yield_unit')->label('Yield Unit (kg/bags/tonnes)')->maxLength(50),

                    TextInput::make('seed_rate_per_acre')
                        ->label('Seed Rate / Acre')
                        ->numeric()
                        ->step(0.001),
                    TextInput::make('seed_unit')->label('Seed Unit (kg/seeds/packets)')->maxLength(50),

                    TextInput::make('spacing_cm')->label('Plant Spacing (cm)')->numeric()->step(0.01),
                ]),

            Section::make('Description & Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('crop_name')->searchable()->sortable(),
                TextColumn::make('variety_name')->searchable()->limit(30),
                TextColumn::make('seed_supplier')->toggleable(),
                TextColumn::make('planting_season')->toggleable(),
                TextColumn::make('growing_period_days')->label('Days'),
                TextColumn::make('typical_yield_per_acre')
                    ->label('Yield/Acre')
                    ->formatStateUsing(fn ($record) => $record->typical_yield_per_acre
                        ? number_format($record->typical_yield_per_acre, 2) . ' ' . ($record->yield_unit ?? '')
                        : '—'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active only'),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('crop_name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCropVarieties::route('/'),
            'create' => Pages\CreateCropVariety::route('/create'),
            'view'   => Pages\ViewCropVariety::route('/{record}'),
            'edit'   => Pages\EditCropVariety::route('/{record}/edit'),
        ];
    }
}
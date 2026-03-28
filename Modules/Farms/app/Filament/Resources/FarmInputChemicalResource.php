<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmInputChemicalResource\Pages;
use Modules\Farms\Models\FarmInputChemical;

class FarmInputChemicalResource extends Resource
{
    protected static ?string $model = FarmInputChemical::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm Operations';

    protected static ?string $navigationLabel = 'Chemical Library';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product Information')
                ->columns(2)
                ->schema([
                    TextInput::make('product_name')->required()->maxLength(200),
                    TextInput::make('brand_name')->nullable()->maxLength(200),
                    TextInput::make('active_ingredient')->nullable()->maxLength(200),

                    Select::make('chemical_class')
                        ->options(array_combine(
                            FarmInputChemical::CHEMICAL_CLASSES,
                            FarmInputChemical::CHEMICAL_CLASSES,
                        ))
                        ->nullable(),

                    TextInput::make('registration_number')->nullable()->maxLength(100),
                    TextInput::make('application_rate_per_ha')->label('Application Rate (per ha)')->nullable()->maxLength(100),
                ]),

            Section::make('Safety & Compliance')
                ->columns(3)
                ->schema([
                    TextInput::make('phi_days')->label('PHI (days)')->numeric()->integer()->nullable(),
                    TextInput::make('mrl_mg_per_kg')->label('MRL (mg/kg)')->numeric()->step(0.0001)->nullable(),
                    Toggle::make('approved_for_organic')->label('Approved for Organic'),
                    Toggle::make('is_restricted')->label('Restricted Use'),
                    Toggle::make('is_active')->label('Active')->default(true),
                ]),

            Section::make('Safety Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('safety_notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')->sortable()->searchable(),
                TextColumn::make('brand_name')->toggleable(),
                TextColumn::make('chemical_class')->badge()->color('info'),
                TextColumn::make('phi_days')->label('PHI Days')->numeric(),
                IconColumn::make('approved_for_organic')->label('Organic')->boolean(),
                IconColumn::make('is_restricted')->label('Restricted')->boolean(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('chemical_class')->options(
                    array_combine(FarmInputChemical::CHEMICAL_CLASSES, FarmInputChemical::CHEMICAL_CLASSES)
                ),
                TernaryFilter::make('approved_for_organic'),
                TernaryFilter::make('is_active'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('product_name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmInputChemicals::route('/'),
            'create' => Pages\CreateFarmInputChemical::route('/create'),
            'edit'   => Pages\EditFarmInputChemical::route('/{record}/edit'),
        ];
    }
}

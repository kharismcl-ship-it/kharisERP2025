<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Construction\Filament\Resources\MaterialUsageResource\Pages;
use Modules\Construction\Models\MaterialUsage;

class MaterialUsageResource extends Resource
{
    protected static ?string $model = MaterialUsage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Material Usage';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Material Details')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('project_phase_id')
                        ->label('Phase')
                        ->relationship('phase', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('material_name')
                        ->label('Material Name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('unit')
                        ->maxLength(50),
                ]),
            ]),

            Section::make('Quantities & Cost')->schema([
                Grid::make(2)->schema([
                    DatePicker::make('usage_date')
                        ->label('Usage Date')
                        ->required(),
                    TextInput::make('supplier')
                        ->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('quantity')
                        ->numeric()
                        ->step(0.001)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            $qty = (float) $state;
                            $cost = (float) $get('unit_cost');
                            if ($qty && $cost) {
                                $set('total_cost', round($qty * $cost, 2));
                            }
                        }),
                    TextInput::make('unit_cost')
                        ->label('Unit Cost')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.0001)
                        ->live()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            $qty = (float) $get('quantity');
                            $cost = (float) $state;
                            if ($qty && $cost) {
                                $set('total_cost', round($qty * $cost, 2));
                            }
                        }),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('total_cost')
                        ->label('Total Cost')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->readOnly(),
                ]),
            ]),

            Section::make('Notes')->schema([
                Textarea::make('notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phase.name')
                    ->label('Phase')
                    ->placeholder('—'),
                TextColumn::make('usage_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('material_name')
                    ->label('Material')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 3) . ($record->unit ? ' ' . $record->unit : '')),
                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('GHS'),
                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->money('GHS')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('construction_project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('project_phase_id')
                    ->label('Phase')
                    ->relationship('phase', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('usage_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaterialUsages::route('/'),
            'create' => Pages\CreateMaterialUsage::route('/create'),
            'view'   => Pages\ViewMaterialUsage::route('/{record}'),
            'edit'   => Pages\EditMaterialUsage::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['material_name', 'supplier'];
    }
}
